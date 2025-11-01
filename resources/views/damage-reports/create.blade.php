<x-layout>
    <x-slot name="title">Lapor Kerusakan</x-slot>
    <x-slot name="page_heading">Lapor Kerusakan Barang</x-slot>

    <div class="section-body">
        @include('utilities.alert')

        <div class="card">
            <div class="card-header"><h4>Form Laporan</h4></div>
            <div class="card-body">
                <form action="{{ route('kerusakan.store') }}" method="POST" enctype="multipart/form-data">
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
                    <div class="form-group">
                        <label>Bukti Gambar (opsional)</label>
                        <input type="file" name="evidence" accept="image/png,image/jpeg,image/jpg,image/webp" class="form-control-file">
                        <small class="text-muted">Format: JPG/PNG/WebP, maks 2 MB.</small>
                        <div class="mt-2" id="evidencePreview" style="display:none;">
                            <img src="#" alt="Preview" class="img-thumbnail" style="max-height:200px;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Tambahan Bukti (boleh lebih dari satu)</label>
                        <input type="file" name="evidences[]" accept="image/png,image/jpeg,image/jpg,image/webp" class="form-control-file" multiple>
                        <div class="mt-2 d-flex flex-wrap" id="evidencesPreview"></div>
                        <small class="text-muted d-block">Setiap file maks 4 MB.</small>
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

            // Preview evidence image
            $("input[name='evidence']").on('change', function(evt){
                const file = this.files && this.files[0];
                if (!file) { $('#evidencePreview').hide(); return; }
                const reader = new FileReader();
                reader.onload = function(e){
                    $('#evidencePreview img').attr('src', e.target.result);
                    $('#evidencePreview').show();
                };
                reader.readAsDataURL(file);
            });

            // Preview multiple evidences
            $("input[name='evidences[]']").on('change', function(){
                const container = $('#evidencesPreview');
                container.empty();
                const files = this.files || [];
                Array.from(files).forEach(function(file){
                    const reader = new FileReader();
                    reader.onload = function(e){
                        const img = $('<img class="img-thumbnail mr-2 mb-2" style="height:90px;width:90px;object-fit:cover;">');
                        img.attr('src', e.target.result);
                        container.append(img);
                    };
                    reader.readAsDataURL(file);
                });
            });
        });
    </script>
    @endpush
</x-layout>
