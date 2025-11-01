<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AdminUtilityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function storageLink(Request $request)
    {
        if (! method_exists($request->user(), 'hasRole') || ! $request->user()->hasRole('Administrator')) {
            return back()->with('error', 'Hanya Administrator yang dapat melakukan aksi ini.');
        }

        try {
            // Ensure target exists
            if (! File::exists(storage_path('app/public'))) {
                File::makeDirectory(storage_path('app/public'), 0755, true);
            }
            // Try artisan first
            Artisan::call('storage:link');
            // Quick verification
            if (File::exists(public_path('storage'))) {
                return back()->with('success', 'Symlink storage berhasil atau sudah ada.');
            }
        } catch (\Throwable $e) {
            // fallback below
        }

        // Fallback: attempt manual symlink
        try {
            $target = storage_path('app/public');
            $link = public_path('storage');
            if (! file_exists($link)) {
                @symlink($target, $link);
            }
            if (file_exists($link)) {
                return back()->with('success', 'Symlink storage dibuat (manual).');
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membuat symlink. Jalankan php artisan storage:link secara manual sebagai Administrator.');
        }

        return back()->with('info', 'Cek kembali hak akses OS Anda.');
    }
}

