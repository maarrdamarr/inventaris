<x-layout>
    <x-slot name="title">Transaksi</x-slot>
    <x-slot name="page_heading">Transaksi Keuangan</x-slot>

    <div class="card">
        <div class="card-body table-responsive">
            @php $rows = \App\FinanceTransaction::latest()->take(100)->get(); @endphp
            <table class="table table-striped" id="trxTable" style="width:100%">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Arah</th>
                        <th>Nominal</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $r)
                    <tr>
                        <td>{{ $r->created_at?->format('Y-m-d H:i') }}</td>
                        <td>{{ ucfirst($r->type) }}</td>
                        <td>{{ strtoupper($r->direction) }}</td>
                        <td>Rp{{ number_format($r->amount) }}</td>
                        <td>{{ $r->note }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('js')
    <script> $(function(){ $('#trxTable').DataTable({ pageLength: 10 }); }); </script>
    @endpush
</x-layout>

