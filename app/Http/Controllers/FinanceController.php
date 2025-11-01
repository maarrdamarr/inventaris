<?php

namespace App\Http\Controllers;

use App\DamageReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FinanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $totalFines = DamageReport::whereNotNull('fine_amount')->sum('fine_amount');
        $unpaidCount = DamageReport::where('fine_status', 'unpaid')->count();
        $submittedCount = DamageReport::where('fine_status', 'submitted')->count();
        $balance = \App\FinanceAccount::query()->first();

        return view('finance.index', [
            'title' => 'Keuangan',
            'page_heading' => 'Ringkasan Keuangan',
            'summary' => compact('totalFines','unpaidCount','submittedCount'),
            'balance' => $balance,
        ]);
    }

    public function purchases()
    {
        return view('finance.purchases', [
            'title' => 'Pembelian',
            'page_heading' => 'Data Pembelian',
        ]);
    }

    public function fines()
    {
        $fines = DamageReport::with(['reporter','commodity'])
            ->whereNotNull('fine_amount')
            ->latest()->get();

        return view('finance.fines', [
            'title' => 'Denda',
            'page_heading' => 'Manajemen Denda',
            'fines' => $fines,
        ]);
    }

    public function payFine()
    {
        $user = Auth::user();
        $items = DamageReport::where('reporter_id', $user->id)
            ->whereNotNull('fine_amount')
            ->latest()->get();

        return view('finance.pay-fine', [
            'title' => 'Bayar Denda',
            'page_heading' => 'Bayar Denda',
            'items' => $items,
        ]);
    }

    public function submitFineProof(Request $request, DamageReport $damage)
    {
        $request->validate(['proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096']);
        if ($damage->reporter_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak berhak mengunggah bukti untuk laporan ini.');
        }
        if (is_null($damage->fine_amount)) {
            return back()->with('error', 'Laporan ini tidak memiliki denda.');
        }
        $path = $request->file('proof')->store('finance/fine-proof', 'public');
        $damage->update([
            'fine_proof_path' => $path,
            'fine_status' => 'submitted',
        ]);
        return back()->with('success', 'Bukti pembayaran denda telah dikirim.');
    }

    public function approveFine(Request $request, DamageReport $damage)
    {
        // Sederhana: cek role admin/staff
        $user = Auth::user();
        if (! (method_exists($user,'hasRole') && ($user->hasRole('Administrator') || $user->hasRole('Staff TU (Tata Usaha)') || $user->hasRole('CS Sekolah')))) {
            return back()->with('error', 'Tidak berhak menyetujui.');
        }
        if (is_null($damage->fine_amount)) {
            return back()->with('error', 'Tidak ada denda pada laporan ini.');
        }
        $damage->update([
            'fine_status' => 'approved',
            'fine_paid_at' => now(),
        ]);
        // Catat transaksi + update kas
        try {
            $acc = \App\FinanceAccount::query()->first();
            if ($acc) {
                $acc->increment('cash_balance', (int) $damage->fine_amount);
            }
            \App\FinanceTransaction::create([
                'type' => 'fine',
                'direction' => 'in',
                'amount' => (int) $damage->fine_amount,
                'reference_id' => $damage->id,
                'reference_type' => 'damage_reports',
                'note' => 'Pembayaran denda laporan kerusakan',
            ]);
        } catch (\Throwable $e) {}
        return back()->with('success', 'Denda ditandai sudah dibayar.');
    }

    public function setFine(Request $request, DamageReport $damage)
    {
        $user = Auth::user();
        if (! (method_exists($user,'hasRole') && ($user->hasRole('Administrator') || $user->hasRole('CS Sekolah')))) {
            return back()->with('error','Tidak berhak mengubah denda.');
        }
        $data = $request->validate([
            'fine_amount' => 'required|integer|min:0',
            'payment_method' => 'nullable|string|max:50',
            'payment_type' => 'nullable|in:cash,installment',
            'installment_total' => 'nullable|integer|min:1',
        ]);
        $damage->update(array_merge($data, ['fine_status' => $damage->fine_status ?? 'unpaid']));
        return back()->with('success','Denda diperbarui.');
    }

    public function csCheck(Request $request, DamageReport $damage)
    {
        $user = Auth::user();
        if (! (method_exists($user,'hasRole') && ($user->hasRole('CS Sekolah') || $user->hasRole('Administrator')))) {
            return back()->with('error','Tidak berhak.');
        }
        $damage->update(['fine_status' => 'checked', 'cs_checked_at' => now()]);
        return back()->with('success','Bukti diperiksa CS. Menunggu konfirmasi admin.');
    }

    public function sendMessage(Request $request, DamageReport $damage)
    {
        $data = $request->validate(['message' => 'required|string|min:2']);
        $msg = \App\DamageFineMessage::create([
            'damage_report_id' => $damage->id,
            'sender_id' => Auth::id(),
            'recipient_id' => $damage->reporter_id,
            'message' => $data['message'],
        ]);
        try { $damage->reporter?->notify(new \App\Notifications\DamageFineMessageCreated($msg)); } catch (\Throwable $e) {}
        return back()->with('success','Pesan dikirim ke pelapor.');
    }
}
