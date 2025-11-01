<x-layout>
    <x-slot name="title">Denda</x-slot>
    <x-slot name="page_heading">Manajemen Denda</x-slot>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped" id="fineTable" style="width:100%">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Pelapor</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th>Bukti</th>
                        <th>Set Denda</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fines as $f)
                    <tr>
                        <td><a href="{{ route('kerusakan.show', $f) }}">{{ $f->title }}</a></td>
                        <td>{{ $f->reporter->name ?? '-' }}</td>
                        <td>Rp{{ number_format($f->fine_amount) }}</td>
                        <td>
                            <span class="badge badge-{{ ['unpaid'=>'secondary','submitted'=>'warning','approved'=>'success'][$f->fine_status] ?? 'light' }}">{{ ucfirst($f->fine_status) }}</span>
                        </td>
                        <td>
                            @if($f->fine_proof_path)
                                <a href="{{ Storage::url($f->fine_proof_path) }}" target="_blank">Lihat</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td style="min-width:320px;">
                            <form method="POST" action="{{ route('keuangan.denda.set', $f) }}" class="form-inline">
                                @csrf
                                <input type="number" name="fine_amount" class="form-control form-control-sm mr-1" placeholder="Nominal" value="{{ $f->fine_amount }}" min="0" style="width:120px;">
                                <select name="payment_method" class="form-control form-control-sm mr-1">
                                    @php $pm = $f->payment_method; @endphp
                                    <option value="">Metode</option>
                                    @foreach(['qris'=>'QRIS','tunai'=>'Tunai','transfer_bca'=>'Transfer BCA','transfer_bri'=>'Transfer BRI','gopay'=>'GoPay','ovo'=>'OVO','dana'=>'DANA'] as $k=>$v)
                                        <option value="{{ $k }}" {{ $pm===$k?'selected':'' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                                <select name="payment_type" class="form-control form-control-sm mr-1">
                                    @php $pt = $f->payment_type; @endphp
                                    <option value="cash" {{ $pt==='cash'?'selected':'' }}>Cash</option>
                                    <option value="installment" {{ $pt==='installment'?'selected':'' }}>Cicilan</option>
                                </select>
                                <input type="number" name="installment_total" class="form-control form-control-sm mr-1" placeholder="Cicilan" value="{{ $f->installment_total }}" min="1" style="width:90px;">
                                <button class="btn btn-sm btn-primary">Simpan</button>
                            </form>
                            <form method="POST" action="{{ route('keuangan.denda.message', $f) }}" class="form-inline mt-1">
                                @csrf
                                <input type="text" name="message" class="form-control form-control-sm mr-1" placeholder="Kirim pesan ke pelapor" style="width:220px;" required>
                                <button class="btn btn-sm btn-info">Kirim</button>
                            </form>
                        </td>
                        <td>
                            <div class="d-flex">
                                <form method="POST" action="{{ route('keuangan.denda.check', $f) }}" class="mr-1">
                                    @csrf
                                    <button class="btn btn-sm btn-warning">Validasi CS</button>
                                </form>
                                <form method="POST" action="{{ route('keuangan.denda.approve', $f) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Konfirmasi Admin</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('js')
    <script>
        $(function(){
            $('#fineTable').DataTable({
                lengthMenu: [5, 10, 15, { label: 'All', value: -1 }],
                pageLength: 10
            });
        });
    </script>
    @endpush
</x-layout>
