# Kartu Product & Service

Pada database yang sudah memiliki halaman Product & Service, jalankan setelah memperbarui kode:

```sh
php artisan db:seed --class=ProductServiceCardsSeeder --force
npm run build
php artisan view:clear
```

Seeder hanya membuat section `solutions` beserta dua kartu saat section tersebut belum ada. Pengaturan halaman lain dan perubahan kartu dari admin tidak ditimpa. Tidak ada tabel atau migrasi baru.

Kelola konten melalui `/admin/pages` → Product & Service → section Solar Products & Services (`solutions`). Edit item Solar Component atau Solar Services untuk mengganti Title, Description, Image, Image Alt, Link Text, dan Link URL. Edit section untuk mengubah Title dan Subtitle di atas kedua kartu. Opsi aktif dan urutan item mengikuti pengaturan admin.
