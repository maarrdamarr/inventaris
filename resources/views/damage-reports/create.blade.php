<x-layout>
    <x-slot name="title">Lapor Kerusakan</x-slot>
    <x-slot name="page_heading">Lapor Kerusakan Barang</x-slot>

    <div class="section-body">
        @include('utilities.alert')

        <div class="card">
            <div class="card-header"><h4>Form Laporan</h4></div>
            <div class="card-body">
                <form action="{{ route('kerusakan.store') }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Barang (opsional)</label>
                            <select name="commodity_id" class="form-control select-commodity">
                                <option value="">Pilih jika diketahui</option>
                                @foreach($commodities as $c)
                                    <option value="{{ $c->id }}" {{ old('commodity_id')==$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Tingkat Keparahan</label>
                            <select name="severity" class="form-control" required>
                                <option value="rendah" {{ old('severity')=='rendah' ? 'selected' : '' }}>Rendah</option>
                                <option value="sedang" {{ old('severity','sedang')=='sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="tinggi" {{ old('severity')=='tinggi' ? 'selected' : '' }}>Tinggi</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Judul</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="Contoh: Kabel power putus">
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="Jelaskan kerusakan secara singkat"></textarea>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">Kirim Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        $(function(){
            try {
                new TomSelect('.select-commodity', { create: false, sortField: {field:'text',direction:'asc'} });
            } catch(e) {}
        });
    </script>
    @endpush
</x-layout>

