# Update Meet Our Team

## File yang diupload

Upload file berikut ke lokasi yang sama di server:

- `app/Http/Controllers/Frontend/AboutController.php`
- `app/Http/Requests/Admin/StoreTeamMemberRequest.php`
- `app/Models/TeamMember.php`
- `app/Services/PublicContentService.php`
- `resources/views/public/about.blade.php`
- `resources/views/admin/teams/form.blade.php`
- `resources/views/admin/teams/index.blade.php`
- `resources/css/app.css`
- `database/migrations/2026_10_17_000001_add_ceo_and_social_fields_to_team_members_table.php`
- Seluruh isi `public/build/`, termasuk `manifest.json` dan `assets/`.

Hasil build lokal sudah diperbarui. Jika CSS diubah lagi, jalankan `npm run build` sebelum upload `public/build/`.

## Database dan cache server

Jalankan dari root project Laravel di server setelah file migration diupload, sebelum mengakses fitur Teams baru:

```bash
php artisan migrate --path=database/migrations/2026_10_17_000001_add_ceo_and_social_fields_to_team_members_table.php --force
php artisan view:clear
```

Migration ini hanya menambahkan kolom `is_ceo`, `message_title`, `message`, dan `social_links` ke tabel `team_members`. Anggota yang sudah ada tetap menjadi anggota biasa. Tidak perlu menjalankan seeder atau migrate:fresh.

## Mengisi konten

1. Buka Admin > Teams, tambah atau edit profil CEO.
2. Isi nama, posisi, foto; aktifkan **Display as CEO** dan **Active on frontend**.
3. Isi **Message Title** (opsional, default CEO Message) dan **CEO Message**. Pisahkan paragraf dengan baris kosong.
4. Simpan. CEO tampil di atas dan tidak diulang pada kartu anggota.
5. Untuk anggota biasa, isi Description, foto, posisi, Sort Order, serta link Social Media bila diperlukan. Ikon hanya tampil untuk link yang diisi.

Hanya satu profil boleh dipilih sebagai CEO. Untuk mengganti CEO, matikan pilihan Display as CEO pada profil lama terlebih dahulu.

Section Team pada Admin > Pages > About harus aktif. Periksa halaman `/about#meetTeams` dan lakukan hard refresh (Ctrl + F5) setelah upload aset; bersihkan cache CDN jika digunakan.

## Verifikasi lokal

- Migration penambahan kolom berhasil diterapkan pada database lokal.
- 20 tes Teams dan konten publik lulus (93 assertions).
- Build Vite berhasil.
- Tampilan belum diperiksa langsung melalui browser.
