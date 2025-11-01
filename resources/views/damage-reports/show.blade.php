<x-layout>
    <x-slot name="title">Detail Laporan</x-slot>
    <x-slot name="page_heading">Detail Laporan Kerusakan</x-slot>

    <div class="section-body">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h4>{{ $report->title }}</h4></div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Deskripsi:</strong></p>
                        <p>{{ $report->description }}</p>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Barang:</strong> {{ $report->commodity->name ?? '-' }}</p>
                                <p class="mb-1"><strong>Pelapor:</strong> {{ $report->reporter->name ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Keparahan:</strong> <span class="badge badge-{{ ['rendah'=>'success','sedang'=>'warning','tinggi'=>'danger'][$report->severity] ?? 'secondary' }}">{{ ucfirst($report->severity) }}</span></p>
                                <p class="mb-1"><strong>Status:</strong> <span class="badge badge-{{ ['dilaporkan'=>'secondary','diproses'=>'info','selesai'=>'success'][$report->status] ?? 'light' }}">{{ ucfirst($report->status) }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                @can('kelola kerusakan')
                <div class="card">
                    <div class="card-body">
                        @if($report->status==='dilaporkan')
                            <form method="POST" action="{{ route('kerusakan.start', $report) }}" class="d-inline">@csrf<button class="btn btn-primary">Proses</button></form>
                        @endif
                        @if(in_array($report->status,['dilaporkan','diproses']))
                            <form method="POST" action="{{ route('kerusakan.resolve', $report) }}" class="d-inline">@csrf<button class="btn btn-success">Selesai</button></form>
                        @endif
                        <hr>
                        <form method="POST" action="{{ route('keuangan.denda.set', $report) }}" class="form-inline">
                            @csrf
                            <label class="mr-2 mb-0">Set Denda:</label>
                            <input type="number" name="fine_amount" class="form-control mr-1" placeholder="Nominal" value="{{ $report->fine_amount }}" min="0" style="max-width:160px;">
                            <select name="payment_method" class="form-control mr-1">
                                @php $pm = $report->payment_method; @endphp
                                <option value="">Metode</option>
                                @foreach(['qris'=>'QRIS','tunai'=>'Tunai','transfer_bca'=>'Transfer BCA','transfer_bri'=>'Transfer BRI','gopay'=>'GoPay','ovo'=>'OVO','dana'=>'DANA'] as $k=>$v)
                                    <option value="{{ $k }}" {{ $pm===$k?'selected':'' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                            <select name="payment_type" class="form-control mr-1">
                                @php $pt = $report->payment_type; @endphp
                                <option value="cash" {{ $pt==='cash'?'selected':'' }}>Cash</option>
                                <option value="installment" {{ $pt==='installment'?'selected':'' }}>Cicilan</option>
                            </select>
                            <input type="number" name="installment_total" class="form-control mr-1" placeholder="Cicilan" value="{{ $report->installment_total }}" min="1" style="max-width:120px;">
                            <button class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
                @endcan
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h4>Bukti</h4></div>
                    <div class="card-body">
                        @if($report->evidence_path)
                            <a href="{{ Storage::url($report->evidence_path) }}" target="_blank">
                                <img src="{{ Storage::url($report->evidence_path) }}" class="img-fluid mb-2" alt="bukti utama" onerror="this.onerror=null;this.src='https://via.placeholder.com/600x300?text=Tidak+ada+gambar';">
                            </a>
                        @endif
                        <div class="d-flex flex-wrap">
                            @foreach($report->files as $f)
                                <div class="mr-2 mb-2 position-relative" style="display:inline-block;">
                                    <a href="{{ Storage::url($f->path) }}" target="_blank">
                                        <img src="{{ Storage::url($f->path) }}" style="height:90px;width:90px;object-fit:cover;border-radius:6px;" alt="bukti" onerror="this.onerror=null;this.src='https://via.placeholder.com/90?text=No+Img';">
                                    </a>
                                    @can('kelola kerusakan')
                                    <form method="POST" action="{{ route('kerusakan.evidence.delete', $f) }}" style="position:absolute;top:-6px;right:-6px;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" style="padding:2px 6px;border-radius:50%;" title="Hapus">&times;</button>
                                    </form>
                                    @endcan
                                </div>
                            @endforeach
                            @if(!$report->evidence_path && $report->files->isEmpty())
                                <span class="text-muted">Tidak ada bukti.</span>
                            @endif
                        </div>
                        @can('kelola kerusakan')
                        <hr>
                        <form method="POST" action="{{ route('kerusakan.evidence.add', $report) }}" enctype="multipart/form-data">
                            @csrf
                            <label class="mb-1">Tambah Bukti</label>
                            <div class="d-flex">
                                <input type="file" name="file" accept="image/png,image/jpeg,image/jpg,image/webp" class="form-control" required>
                                <button class="btn btn-primary ml-2">Upload</button>
                            </div>
                            <small class="text-muted">Maks 4 MB.</small>
                        </form>
                        @endcan
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h4>Aktivitas</h4></div>
                    <div class="card-body">
                        @forelse($report->comments as $c)
                            <div class="media mb-3">
                                <div class="media-body">
                                    <div>
                                        <strong>{{ $c->user->name ?? 'Pengguna' }}</strong>
                                        <small class="text-muted">{{ $c->created_at?->diffForHumans() }}</small>
                                    </div>
                                    @if($c->status_change)
                                        <div><span class="badge badge-info">Status: {{ ucfirst($c->status_change) }}</span></div>
                                    @endif
                                    @if($c->comment)
                                        <div>{{ $c->comment }}</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-muted">Belum ada aktivitas.</div>
                        @endforelse
                        <hr>
                        <form method="POST" action="{{ route('kerusakan.comment', $report) }}">
                            @csrf
                            <div class="form-group mb-2">
                                <textarea name="comment" class="form-control" rows="2" placeholder="Tulis komentar..." required></textarea>
                            </div>
                            <div class="text-right">
                                <button class="btn btn-secondary btn-sm">Kirim</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
