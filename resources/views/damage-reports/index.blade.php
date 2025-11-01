<x-layout>
    <x-slot name="title">Laporan Kerusakan</x-slot>
    <x-slot name="page_heading">Kelola Laporan Kerusakan</x-slot>

    <div class="section-body">
        <x-filter resetFilterURL="{{ route('kerusakan.index') }}">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua</option>
                        @foreach(['dilaporkan','diproses','selesai'] as $st)
                            <option value="{{ $st }}" {{ request('status')===$st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Keparahan</label>
                    <select name="severity" class="form-control">
                        <option value="">Semua</option>
                        @foreach(['rendah','sedang','tinggi'] as $sv)
                            <option value="{{ $sv }}" {{ request('severity')===$sv ? 'selected' : '' }}>{{ ucfirst($sv) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Cari</label>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Judul/Deskripsi">
                </div>
            </div>
        </x-filter>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Daftar Laporan</h4>
                <a href="{{ route('kerusakan.create') }}" class="btn btn-primary">+ Lapor Baru</a>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped" id="damageTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Barang</th>
                            <th>Pelapor</th>
                            <th>Keparahan</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $r)
                            <tr>
                                <td>{{ $r->title }}</td>
                                <td>{{ $r->commodity->name ?? '-' }}</td>
                                <td>{{ $r->reporter->name ?? '-' }}</td>
                                <td><span class="badge badge-{{ ['rendah'=>'success','sedang'=>'warning','tinggi'=>'danger'][$r->severity] ?? 'secondary' }}">{{ ucfirst($r->severity) }}</span></td>
                                <td><span class="badge badge-{{ ['dilaporkan'=>'secondary','diproses'=>'info','selesai'=>'success'][$r->status] ?? 'light' }}">{{ ucfirst($r->status) }}</span></td>
                                <td>{{ $r->created_at?->format('Y-m-d') }}</td>
                                <td class="d-flex">
                                    @can('kelola kerusakan')
                                        @if($r->status==='dilaporkan')
                                            <form method="POST" action="{{ route('kerusakan.start', $r) }}" class="mr-1">@csrf<button class="btn btn-sm btn-primary">Proses</button></form>
                                        @endif
                                        @if(in_array($r->status,['dilaporkan','diproses']))
                                            <form method="POST" action="{{ route('kerusakan.resolve', $r) }}">@csrf<button class="btn btn-sm btn-success">Selesai</button></form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        $(function(){
            $('#damageTable').DataTable({
                lengthMenu: [5, 10, 15, { label: 'All', value: -1 }],
                pageLength: 10,
                language: { emptyTable: 'Belum ada laporan.' }
            });
        });
    </script>
    @endpush
</x-layout>

