<x-layout>
    <x-slot name="title">Bayar Denda</x-slot>
    <x-slot name="page_heading">Bayar Denda</x-slot>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped" id="payFineTable" style="width:100%">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $it)
                    <tr>
                        <td><a href="{{ route('kerusakan.show', $it) }}">{{ $it->title }}</a></td>
                        <td>Rp{{ number_format($it->fine_amount) }}</td>
                        <td><span class="badge badge-{{ ['unpaid'=>'secondary','submitted'=>'warning','approved'=>'success'][$it->fine_status] ?? 'light' }}">{{ ucfirst($it->fine_status) }}</span></td>
                        <td>
                            @if($it->fine_proof_path)
                                <a href="{{ Storage::url($it->fine_proof_path) }}" target="_blank">Lihat</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($it->fine_status !== 'approved')
                            <form method="POST" action="{{ route('keuangan.denda.submit', $it) }}" enctype="multipart/form-data" class="d-flex align-items-center">
                                @csrf
                                <input type="file" name="proof" accept="image/png,image/jpeg,image/jpg,image/webp" class="form-control" required>
                                <button class="btn btn-primary btn-sm ml-2">Kirim Bukti</button>
                            </form>
                            @else
                                <em class="text-muted">Lunas</em>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted">Tidak ada denda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('js')
    <script>
        $(function(){ $('#payFineTable').DataTable({ pageLength: 10 }); });
    </script>
    @endpush
</x-layout>

