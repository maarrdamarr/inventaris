<x-layout>
    <x-slot name="title">Kelola Peminjaman</x-slot>
    <x-slot name="page_heading">Kelola Pengajuan Peminjaman</x-slot>

    <div class="section-body">
        <x-filter resetFilterURL="{{ route('peminjaman.manage') }}">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Status</label>
                    <select class="form-control" name="status">
                        <option value="">Semua</option>
                        @foreach(['pending','approved','rejected','returned'] as $st)
                            <option value="{{ $st }}" {{ request('status')===$st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-filter>

        <div class="card">
            <div class="card-header">
                <h4>Daftar Pengajuan</h4>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped" id="manageBorrowTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Pemohon</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tgl Ajukan</th>
                            <th>Batas Kembali</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($borrowings as $b)
                            <tr>
                                <td>{{ $b->user->name ?? '-' }}</td>
                                <td>{{ $b->commodity->name ?? '-' }}</td>
                                <td>{{ $b->quantity }}</td>
                                <td><span class="badge badge-{{ ['pending'=>'warning','approved'=>'primary','rejected'=>'secondary','returned'=>'success'][$b->status] ?? 'light' }}">{{ ucfirst($b->status) }}</span></td>
                                <td>{{ $b->created_at?->format('Y-m-d') }}</td>
                                <td>{{ $b->due_at }}</td>
                                <td class="d-flex">
                                    @can('kelola peminjaman')
                                        @if($b->status==='pending')
                                            <form method="POST" action="{{ route('peminjaman.approve', $b) }}" class="mr-1">
                                                @csrf
                                                <button class="btn btn-success btn-sm">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('peminjaman.reject', $b) }}">
                                                @csrf
                                                <button class="btn btn-danger btn-sm">Reject</button>
                                            </form>
                                        @elseif($b->status==='approved')
                                            <form method="POST" action="{{ route('peminjaman.returned', $b) }}">
                                                @csrf
                                                <button class="btn btn-info btn-sm">Tandai Dikembalikan</button>
                                            </form>
                                        @else
                                            <em class="text-muted">—</em>
                                        @endif
                                    @else
                                        <em class="text-muted">—</em>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">Belum ada pengajuan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        $(function(){
            $('#manageBorrowTable').DataTable({
                lengthMenu: [5, 10, 15, { label: 'All', value: -1 }],
                pageLength: 10
            });
        });
    </script>
    @endpush
</x-layout>
