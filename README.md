### Langkah-langkah instalasi

-   Clone repository ini

```bash
$ git clone https://github.com/mrizkimaulidan/inven-bs.git
```

-   Install seluruh packages yang dibutuhkan

```bash
$ composer install
```

-   Siapkan database dan atur file .env sesuai dengan konfigurasi Anda

-   Masukan nama sekolah pada konfigurasi .env untuk menampilkan nama sekolah pada print barang. Berikan tanda kutip jika nama sekolah mengandung spasi

Contoh:

```
NAMA_SEKOLAH="SD Negeri 001 Ciledug"
```

-   Jika sudah, migrate seluruh migrasi dan seeding data

```bash
$ php artisan migrate --seed
```

-   Jalankan local server

```
$ php artisan serve
```

-   User default aplikasi untuk login

Administrator

```
Email       : admin@mail.com
Password    : secret
```

Staff TU (Tata Usaha)

```
Email       : stafftu@mail.com
Password    : secret
```

### Dibuat dengan

-   [Laravel](https://laravel.com) - Web Framework
