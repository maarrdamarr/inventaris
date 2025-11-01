<?php

namespace App\Http\Controllers;

use App\Commodity;
use App\CommodityLocation;
use App\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Commodity::with('commodity_location')
            ->where('condition', 1) // Baik
            ->where('quantity', '>', 0);

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('brand', 'like', "%{$q}%")
                    ->orWhere('material', 'like', "%{$q}%");
            });
        }

        if ($request->filled('location')) {
            $query->where('commodity_location_id', $request->input('location'));
        }

        if ($request->filled('material')) {
            $query->where('material', $request->input('material'));
        }

        $items = $query->orderBy('name')->get();

        $locations = CommodityLocation::orderBy('name')->get(['id', 'name']);
        $materials = Commodity::select('material')->distinct()->orderBy('material')->pluck('material');

        return view('borrowings.index', [
            'title' => 'Peminjaman',
            'page_heading' => 'Peminjaman Barang',
            'items' => $items,
            'locations' => $locations,
            'materials' => $materials,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'commodity_id' => 'required|exists:commodities,id',
            'quantity' => 'required|integer|min:1',
            'due_at' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $commodity = Commodity::findOrFail($data['commodity_id']);
        if ($commodity->condition !== 1 || $commodity->quantity < $data['quantity']) {
            return back()->with('error', 'Stok tidak mencukupi atau kondisi barang tidak layak pinjam.');
        }

        Borrowing::create([
            'commodity_id' => $commodity->id,
            'user_id' => Auth::id(),
            'quantity' => $data['quantity'],
            'due_at' => $data['due_at'] ?? null,
            'status' => 'pending',
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Pengajuan peminjaman dikirim. Menunggu persetujuan.');
    }

    // Admin: list all borrowings
    public function manageIndex(Request $request)
    {
        $q = Borrowing::with(['commodity', 'user'])
            ->when($request->filled('status'), fn($x) => $x->where('status', $request->status))
            ->latest()
            ->get();

        return view('borrowings.manage', [
            'title' => 'Kelola Peminjaman',
            'page_heading' => 'Kelola Pengajuan Peminjaman',
            'borrowings' => $q,
        ]);
    }

    // User: my history
    public function myIndex()
    {
        $q = Borrowing::with('commodity')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('borrowings.my', [
            'title' => 'Riwayat Peminjaman',
            'page_heading' => 'Riwayat Peminjaman Saya',
            'borrowings' => $q,
        ]);
    }

    public function approve(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan pending yang bisa di-approve.');
        }
        return DB::transaction(function () use ($borrowing) {
            $commodity = $borrowing->commodity;
            if ($commodity->condition !== 1 || $commodity->quantity < $borrowing->quantity) {
                return back()->with('error', 'Stok tidak mencukupi atau kondisi tidak layak.');
            }
            $commodity->decrement('quantity', $borrowing->quantity);
            $borrowing->update([
                'status' => 'approved',
                'borrowed_at' => now(),
            ]);
            return back()->with('success', 'Pengajuan disetujui.');
        });
    }

    public function reject(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan pending yang bisa ditolak.');
        }
        $borrowing->update(['status' => 'rejected']);
        return back()->with('success', 'Pengajuan ditolak.');
    }

    public function returned(Borrowing $borrowing)
    {
        if (! in_array($borrowing->status, ['approved'])) {
            return back()->with('error', 'Hanya peminjaman yang disetujui yang bisa dikembalikan.');
        }
        return DB::transaction(function () use ($borrowing) {
            $borrowing->commodity->increment('quantity', $borrowing->quantity);
            $borrowing->update([
                'status' => 'returned',
                'returned_at' => now(),
            ]);
            return back()->with('success', 'Barang ditandai sudah dikembalikan.');
        });
    }
}
