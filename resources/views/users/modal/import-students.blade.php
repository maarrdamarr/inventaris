<div class="modal fade" id="user_import_students_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
     aria-labelledby="importStudentsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importStudentsLabel">Import Siswa dari CSV</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Unggah file CSV dengan format kolom:</p>
                <pre class="bg-light p-2"><code>name,email,password
Budi Santoso,budi@example.com,rahasia123
Siti Aminah,siti@example.com,
</code></pre>
                <ul class="text-muted small mb-3">
                    <li>Baris pertama sebagai header dianjurkan.</li>
                    <li>Password opsional; bila kosong akan diisi default <code>password</code>.</li>
                    <li>Duplikasi email akan dilewati.</li>
                </ul>
                <form action="{{ route('pengguna.import-siswa') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Pilih File CSV</label>
                        <input type="file" name="file" accept=".csv,text/csv" class="form-control" required>
                    </div>
                    <div class="modal-footer px-0">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

