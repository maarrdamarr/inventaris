<x-layout>
    <x-slot name="title">Keuangan</x-slot>
    <x-slot name="page_heading">Ringkasan Keuangan</x-slot>

    <div class="row mb-3">
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary"><i class="fas fa-sack-dollar"></i></div>
                <div class="card-wrap">
                    <div class="card-header"><h4>Kas Keuangan</h4></div>
                    <div class="card-body">Rp{{ number_format(($balance->cash_balance ?? 1000000000)) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="card-wrap">
                    <div class="card-header"><h4>Total Nominal Denda</h4></div>
                    <div class="card-body">Rp{{ number_format($summary['totalFines'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info"><i class="fas fa-clock"></i></div>
                <div class="card-wrap">
                    <div class="card-header"><h4>Denda Belum Dibayar</h4></div>
                    <div class="card-body">{{ $summary['unpaidCount'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info"><i class="fas fa-paper-plane"></i></div>
                <div class="card-wrap">
                    <div class="card-header"><h4>Bukti Dikirim</h4></div>
                    <div class="card-body">{{ $summary['submittedCount'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <p class="text-muted mb-2">Pintasan:</p>
            <a href="{{ route('keuangan.pembelian') }}" class="btn btn-outline-primary mr-2"><i class="fas fa-cart-shopping"></i> Pembelian</a>
            <a href="{{ route('keuangan.denda') }}" class="btn btn-outline-primary mr-2"><i class="fas fa-file-invoice-dollar"></i> Denda</a>
            <a href="{{ route('keuangan.bayar-denda') }}" class="btn btn-primary"><i class="fas fa-money-bill-wave"></i> Bayar Denda</a>
            <a href="{{ route('keuangan.transaksi') }}" class="btn btn-outline-primary ml-2"><i class="fas fa-receipt"></i> Transaksi</a>
        </div>
    </div>
</x-layout>
