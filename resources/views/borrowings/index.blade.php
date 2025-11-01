<x-layout>
    <x-slot name="title">Peminjaman</x-slot>
    <x-slot name="page_heading">Peminjaman Barang</x-slot>

    <div class="section-body">
        <div class="row mb-3">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary"><i class="fas fa-box-open"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Total Bisa Dipinjam</h4></div>
                        <div class="card-body">{{ $items->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success"><i class="fas fa-warehouse"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Lokasi Tersedia</h4></div>
                        <div class="card-body">{{ $locations->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info"><i class="fas fa-shapes"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Jenis Material</h4></div>
                        <div class="card-body">{{ $materials->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 d-flex">
            <a href="{{ route('peminjaman.my') }}" class="btn btn-outline-primary mr-2"><i class="fas fa-list"></i> Riwayat Saya</a>
            @can('kelola peminjaman')
            <a href="{{ route('peminjaman.manage') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tasks"></i> Kelola Pengajuan
                @if(isset($pendingCount))
                    <span class="badge badge-danger ml-1">{{ $pendingCount }}</span>
                @endif
            </a>
            @endcan
        </div>

        <x-filter resetFilterURL="{{ route('peminjaman.index') }}">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Cari (nama/merk/material)</label>
                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="contoh: proyektor, acer, besi">
                </div>
                <div class="form-group col-md-4">
                    <label>Lokasi</label>
                    <select name="location" class="form-control">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ (string)request('location')===(string)$loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Material</label>
                    <select name="material" class="form-control">
                        <option value="">Semua</option>
                        @foreach($materials as $mat)
                            <option value="{{ $mat }}" {{ request('material')===$mat ? 'selected' : '' }}>{{ $mat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-filter>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Daftar Barang yang Dapat Dipinjam</h4>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped" id="borrowTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Merk</th>
                            <th>Material</th>
                            <th>Lokasi</th>
                            <th>Kondisi</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->brand }}</td>
                                <td>{{ $item->material }}</td>
                                <td>{{ $item->commodity_location->name ?? '-' }}</td>
                                <td>{{ $item->getConditionName() }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm open-borrow-modal"
                                        data-id="{{ $item->id }}"
                                        data-name="{{ $item->name }}"
                                        data-qty="{{ $item->quantity }}">
                                        <i class="fas fa-hand-holding"></i> Pinjam
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Tidak ada barang yang dapat dipinjam.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('modal')
    <div class="modal fade" id="borrowModal" tabindex="-1" aria-labelledby="borrowModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="borrowModalLabel">Ajukan Peminjaman</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('peminjaman.store') }}">
                    @csrf
                    <input type="hidden" name="commodity_id" id="borrowCommodityId">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Barang</label>
                            <input type="text" id="borrowCommodityName" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label>Jumlah</label>
                            <input type="number" name="quantity" id="borrowQty" class="form-control" min="1" required>
                            <small class="form-text text-muted">Maksimal: <span id="borrowQtyMax">0</span></small>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Kembali (opsional)</label>
                            <input type="date" name="due_at" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Catatan (opsional)</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endpush

    @push('js')
    <script>
        $(function(){
            $('#borrowTable').DataTable({
                lengthMenu: [5, 10, 15, { label: 'All', value: -1 }],
                pageLength: 10,
                order: [[0, 'asc']]
            });

            $(document).on('click', '.open-borrow-modal', function(){
                const id = $(this).data('id');
                const name = $(this).data('name');
                const max = parseInt($(this).data('qty'), 10) || 0;
                $('#borrowCommodityId').val(id);
                $('#borrowCommodityName').val(name);
                $('#borrowQty').attr('max', max).val(1);
                $('#borrowQtyMax').text(max);
                $('#borrowModal').modal('show');
            });
        });
    </script>
    @endpush
</x-layout>
