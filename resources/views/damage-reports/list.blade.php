<x-layout>
    <x-slot name="title">Daftar Kerusakan</x-slot>
    <x-slot name="page_heading">Daftar Laporan Kerusakan</x-slot>

    <div class="section-body">
        <div class="row">
            @forelse($reports as $r)
            <div class="col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0" style="max-width:75%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            <a href="{{ route('kerusakan.show', $r) }}">{{ $r->title }}</a>
                        </h4>
                        <span class="badge badge-{{ ['rendah'=>'success','sedang'=>'warning','tinggi'=>'danger'][$r->severity] ?? 'secondary' }}">
                            {{ ucfirst($r->severity) }}
                        </span>
                    </div>
                    @php
                        $thumb = null;
                        $p = $r->evidence_path ?: optional($r->files->first())->path;
                        if ($p) {
                            $thumbName = basename($p);
                            $thumb = Storage::url('damage-evidence/thumbs/'.$thumbName);
                            $orig = Storage::url($p);
                        }
                        $fallback = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=';
                    @endphp
                    @if($thumb)
                    <div class="card-body p-0">
                        <a href="{{ route('kerusakan.show', $r) }}">
                            <img src="{{ $thumb }}" alt="bukti" style="width:100%;height:220px;object-fit:cover;" loading="lazy" onerror="this.onerror=null;this.src='{{ $orig ?? $fallback }}';">
                        </a>
                    </div>
                    @endif
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <div class="text-muted small">Oleh {{ $r->reporter->name ?? '-' }}</div>
                        <div class="text-muted small">{{ $r->created_at?->format('Y-m-d') }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12"><div class="text-muted">Belum ada laporan.</div></div>
            @endforelse
        </div>
    </div>
</x-layout>

