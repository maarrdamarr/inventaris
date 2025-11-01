<x-layout>
    <x-slot name="title">Laporan</x-slot>
    <x-slot name="page_heading">Laporan</x-slot>

    <div class="section-body">

        <!-- Ringkasan cepat -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary"><i class="fas fa-boxes"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Total Barang</h4></div>
                        <div class="card-body">{{ $summary['total'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success"><i class="fas fa-check-circle"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Baik</h4></div>
                        <div class="card-body">{{ $summary['good'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Kurang Baik</h4></div>
                        <div class="card-body">{{ $summary['not_good'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger"><i class="fas fa-times-circle"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Rusak Berat</h4></div>
                        <div class="card-body">{{ $summary['heavily_damage'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter sederhana -->
        <x-filter resetFilterURL="{{ route('laporan.index') }}">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Dari Tanggal</label>
                    <input type="date" class="form-control" name="from" value="{{ request('from') }}">
                </div>
                <div class="form-group col-md-3">
                    <label>Sampai Tanggal</label>
                    <input type="date" class="form-control" name="to" value="{{ request('to') }}">
                </div>
                <div class="form-group col-md-3">
                    <label>Kondisi</label>
                    <select name="condition" class="form-control">
                        <option value="">Semua</option>
                        <option value="1" {{ request('condition')=='1' ? 'selected' : '' }}>Baik</option>
                        <option value="2" {{ request('condition')=='2' ? 'selected' : '' }}>Kurang Baik</option>
                        <option value="3" {{ request('condition')=='3' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
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

        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <x-bar-chart chartTitle="Jumlah Barang per Tahun Pembelian" chartID="reportChartBar"
                        :series="$charts['year']['series']" :categories="$charts['year']['categories']" />
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <x-pie-chart chartTitle="Komposisi Kondisi Barang" chartID="reportChartPie"
                        :series="$charts['condition']['series']" :categories="$charts['condition']['categories']" />
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Detail Laporan</h4>
                <div>
                    <button class="btn btn-success btn-sm" id="btnExportCsv"><i class="fas fa-file-csv"></i> Export CSV</button>
                    <button class="btn btn-secondary btn-sm" id="btnPrint"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped" id="reportTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Material</th>
                            <th>Kondisi</th>
                            <th>Tanggal Dibuat</th>
                            <th>Harga (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commodities as $c)
                            <tr>
                                <td>{{ $c->name }}</td>
                                <td>{{ $c->material }}</td>
                                <td>{{ $c->getConditionName() }}</td>
                                <td>{{ $c->created_at?->format('Y-m-d') }}</td>
                                <td>{{ $c->indonesian_currency($c->price) }}</td>
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
            const dt = $('#reportTable').DataTable({
                lengthMenu: [5, 10, 15, { label: 'All', value: -1 }],
                pageLength: 10,
            });

            $('#btnPrint').on('click', function(){
                window.print();
            });

            $('#btnExportCsv').on('click', function(){
                const rows = [];
                $('#reportTable thead tr, #reportTable tbody tr').each(function(){
                    const cols = [];
                    $(this).find('th,td').each(function(){
                        let text = $(this).text().replace(/\s+/g,' ').trim();
                        if (text.indexOf(',') !== -1) { text = '"' + text + '"'; }
                        cols.push(text);
                    });
                    rows.push(cols.join(','));
                });
                const blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'laporan.csv';
                a.click();
                URL.revokeObjectURL(url);
            });
        });
    </script>
    @endpush
</x-layout>
