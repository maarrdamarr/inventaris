<?php

namespace App\Http\Controllers;

use App\Commodity;
use App\DamageReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DamageReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // CS Sekolah: manage list
    public function index(Request $request)
    {
        $reports = DamageReport::with(['commodity','reporter'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('severity'), fn($q) => $q->where('severity', $request->severity))
            ->when($request->filled('q'), function ($q) use ($request) {
                $kw = "%{$request->q}%";
                $q->where(function($sub) use ($kw){
                    $sub->where('title','like',$kw)->orWhere('description','like',$kw);
                });
            })
            ->latest()
            ->get();

        return view('damage-reports.index', [
            'title' => 'Laporan Kerusakan',
            'page_heading' => 'Kelola Laporan Kerusakan',
            'reports' => $reports,
        ]);
    }

    // Siswa/Umum: create report
    public function create()
    {
        $commodities = Commodity::orderBy('name')->get(['id','name']);
        return view('damage-reports.create', [
            'title' => 'Lapor Kerusakan',
            'page_heading' => 'Lapor Kerusakan Barang',
            'commodities' => $commodities,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'commodity_id' => 'nullable|exists:commodities,id',
            'title' => 'required|string|max:150',
            'description' => 'required|string|min:10',
            'severity' => 'required|in:rendah,sedang,tinggi',
        ]);

        DamageReport::create([
            'commodity_id' => $data['commodity_id'] ?? null,
            'reporter_id' => Auth::id(),
            'title' => $data['title'],
            'description' => $data['description'],
            'severity' => $data['severity'],
            'status' => 'dilaporkan',
        ]);

        return redirect()->route('kerusakan.create')->with('success', 'Laporan kerusakan berhasil dikirim.');
    }

    public function start(DamageReport $damage)
    {
        if ($damage->status !== 'dilaporkan') {
            return back()->with('error', 'Hanya laporan baru yang bisa diproses.');
        }
        $damage->update(['status' => 'diproses']);
        return back()->with('success', 'Laporan mulai diproses.');
    }

    public function resolve(DamageReport $damage)
    {
        if (! in_array($damage->status, ['dilaporkan','diproses'])) {
            return back()->with('error', 'Status laporan tidak valid untuk diselesaikan.');
        }
        $damage->update(['status' => 'selesai']);
        return back()->with('success', 'Laporan ditandai selesai.');
    }
}

