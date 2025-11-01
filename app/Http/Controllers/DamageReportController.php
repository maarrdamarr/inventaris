<?php

namespace App\Http\Controllers;

use App\Commodity;
use App\DamageReport;
use App\DamageReportFile;
use App\DamageReportComment;
use App\Exports\DamageReportsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class DamageReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // CS Sekolah: manage list
    public function index(Request $request)
    {
        $base = DamageReport::with(['commodity','reporter','files'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('severity'), fn($q) => $q->where('severity', $request->severity))
            ->when($request->filled('q'), function ($q) use ($request) {
                $kw = "%{$request->q}%";
                $q->where(function($sub) use ($kw){
                    $sub->where('title','like',$kw)->orWhere('description','like',$kw);
                });
            });

        $reports = (clone $base)->latest()->get();

        return view('damage-reports.index', [
            'title' => 'Laporan Kerusakan',
            'page_heading' => 'Kelola Laporan Kerusakan',
            'reports' => $reports,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $this->middleware('permission:kelola kerusakan');
        $query = DamageReport::with(['commodity','reporter'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('severity'), fn($q) => $q->where('severity', $request->severity))
            ->when($request->filled('q'), function ($q) use ($request) {
                $kw = "%{$request->q}%";
                $q->where(function($sub) use ($kw){
                    $sub->where('title','like',$kw)->orWhere('description','like',$kw);
                });
            });
        return Excel::download(new DamageReportsExport($query), 'laporan-kerusakan.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $this->middleware('permission:kelola kerusakan');
        $reports = DamageReport::with(['commodity','reporter'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('severity'), fn($q) => $q->where('severity', $request->severity))
            ->when($request->filled('q'), function ($q) use ($request) {
                $kw = "%{$request->q}%";
                $q->where(function($sub) use ($kw){
                    $sub->where('title','like',$kw)->orWhere('description','like',$kw);
                });
            })
            ->latest()->get();
        $pdf = Pdf::loadView('damage-reports.pdf', compact('reports'));
        return $pdf->download('laporan-kerusakan.pdf');
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
            'evidence' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'evidences.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $path = null;
        if ($request->hasFile('evidence')) {
            $path = $request->file('evidence')->store('damage-evidence', 'public');
        }

        $report = DamageReport::create([
            'commodity_id' => $data['commodity_id'] ?? null,
            'reporter_id' => Auth::id(),
            'title' => $data['title'],
            'description' => $data['description'],
            'severity' => $data['severity'],
            'status' => 'dilaporkan',
            'evidence_path' => $path,
        ]);

        // Multiple evidences (optional)
        if ($request->hasFile('evidences')) {
            foreach ($request->file('evidences') as $file) {
                if (!$file) continue;
                $p = $file->store('damage-evidence', 'public');
                $report->files()->create(['path' => $p]);
            }
        }

        return redirect()->route('kerusakan.create')->with('success', 'Laporan kerusakan berhasil dikirim.');
    }

    public function show(DamageReport $damage)
    {
        $damage->load(['commodity','reporter','files']);
        return view('damage-reports.show', [
            'title' => 'Detail Laporan',
            'page_heading' => 'Detail Laporan Kerusakan',
            'report' => $damage,
        ]);
    }

    public function start(DamageReport $damage)
    {
        if ($damage->status !== 'dilaporkan') {
            return back()->with('error', 'Hanya laporan baru yang bisa diproses.');
        }
        $damage->update(['status' => 'diproses']);
        DamageReportComment::create([
            'damage_report_id' => $damage->id,
            'user_id' => Auth::id(),
            'status_change' => 'diproses',
            'comment' => null,
        ]);
        return back()->with('success', 'Laporan mulai diproses.');
    }

    public function resolve(DamageReport $damage)
    {
        if (! in_array($damage->status, ['dilaporkan','diproses'])) {
            return back()->with('error', 'Status laporan tidak valid untuk diselesaikan.');
        }
        $damage->update(['status' => 'selesai']);
        DamageReportComment::create([
            'damage_report_id' => $damage->id,
            'user_id' => Auth::id(),
            'status_change' => 'selesai',
            'comment' => null,
        ]);
        return back()->with('success', 'Laporan ditandai selesai.');
    }

    // Any authenticated user may comment
    public function comment(Request $request, DamageReport $damage)
    {
        $data = $request->validate([
            'comment' => 'required|string|min:2',
        ]);
        DamageReportComment::create([
            'damage_report_id' => $damage->id,
            'user_id' => Auth::id(),
            'comment' => $data['comment'],
        ]);
        return back()->with('success', 'Komentar ditambahkan.');
    }

    // CS: add additional evidence
    public function addEvidence(Request $request, DamageReport $damage)
    {
        $this->authorize('kelola kerusakan');
        $request->validate(['file' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096']);
        $p = $request->file('file')->store('damage-evidence', 'public');
        $damage->files()->create(['path' => $p]);
        return back()->with('success', 'Bukti ditambahkan.');
    }

    public function deleteEvidence(DamageReportFile $file)
    {
        $this->authorize('kelola kerusakan');
        try { Storage::disk('public')->delete($file->path); } catch (\Throwable $e) {}
        $file->delete();
        return back()->with('success', 'Bukti dihapus.');
    }
}
