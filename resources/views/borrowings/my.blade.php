<x-layout>
    <x-slot name="title">Riwayat Peminjaman</x-slot>
    <x-slot name="page_heading">Riwayat Peminjaman Saya</x-slot>

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Riwayat</h4>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped" id="myBorrowTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tgl Ajukan</th>
                            <th>Tgl Pinjam</th>
                            <th>Batas Kembali</th>
                            <th>Tgl Kembali</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($borrowings as $b)
                            <tr>
                                <td>{{ $b->commodity->name ?? '-' }}</td>
                                <td>{{ $b->quantity }}</td>
                                <td><span class="badge badge-{{ ['pending'=>'warning','approved'=>'primary','rejected'=>'secondary','returned'=>'success'][$b->status] ?? 'light' }}">{{ ucfirst($b->status) }}</span></td>
                                <td>{{ $b->created_at?->format('Y-m-d') }}</td>
                                <td>{{ $b->borrowed_at }}</td>
                                <td>{{ $b->due_at }}</td>
                                <td>{{ $b->returned_at }}</td>
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
            $('#myBorrowTable').DataTable({
                lengthMenu: [5, 10, 15, { label: 'All', value: -1 }],
                pageLength: 10,
                language: {
                    emptyTable: 'Belum ada riwayat.'
                }
            });
        });
    </script>
    @endpush
</x-layout>
