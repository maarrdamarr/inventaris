### Langkah-langkah instalasi

-   Clone repository ini

```bash
$ git clone https://github.com/maarrdamarr/inventaris.git
```

-   Install seluruh packages yang dibutuhkan

```bash
$ composer install
```

-   Siapkan database dan atur file .env sesuai dengan konfigurasi Anda

-   Masukan nama sekolah pada konfigurasi .env 

Contoh:

```
NAMA_SEKOLAH="Nama dari kamu"
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

### dengan

-   [Laravel](https://laravel.com) - Web Framework
