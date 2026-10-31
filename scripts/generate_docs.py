import os
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'''
        <w:tcMar {nsdecls("w")}>
            <w:top w:w="{top}" w:type="dxa"/>
            <w:bottom w:w="{bottom}" w:type="dxa"/>
            <w:left w:w="{left}" w:type="dxa"/>
            <w:right w:w="{right}" w:type="dxa"/>
        </w:tcMar>
    ''')
    tcPr.append(tcMar)

def set_callout_border(cell, border_color="0A4568"):
    tcPr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(f'''
        <w:tcBorders {nsdecls("w")}>
            <w:top w:val="none"/>
            <w:left w:val="single" w:sz="24" w:space="0" w:color="{border_color}"/>
            <w:bottom w:val="none"/>
            <w:right w:val="none"/>
        </w:tcBorders>
    ''')
    tcPr.append(borders)

def set_table_borders(table, color="D3D3D3"):
    tblPr = table._tbl.tblPr
    borders = parse_xml(f'''
        <w:tblBorders {nsdecls("w")}>
            <w:top w:val="single" w:sz="6" w:space="0" w:color="{color}"/>
            <w:left w:val="none"/>
            <w:bottom w:val="single" w:sz="8" w:space="0" w:color="{color}"/>
            <w:right w:val="none"/>
            <w:insideH w:val="single" w:sz="4" w:space="0" w:color="{color}"/>
            <w:insideV w:val="none"/>
        </w:tblBorders>
    ''')
    tblPr.append(borders)

def build_documentation(output_path):
    doc = Document()

    # Set page margins (1 inch = 1440 dxa)
    sections = doc.sections
    for section in sections:
        section.top_margin = Inches(1.0)
        section.bottom_margin = Inches(1.0)
        section.left_margin = Inches(1.0)
        section.right_margin = Inches(1.0)

    # Styles
    style_normal = doc.styles['Normal']
    font = style_normal.font
    font.name = 'Calibri'
    font.size = Pt(11)
    font.color.rgb = RGBColor(0x33, 0x33, 0x33)

    # Helper functions
    def add_title(text):
        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p.paragraph_format.space_before = Pt(24)
        p.paragraph_format.space_after = Pt(6)
        run = p.add_run(text)
        run.bold = True
        run.font.size = Pt(24)
        run.font.color.rgb = RGBColor(0x0A, 0x45, 0x68)
        return p

    def add_subtitle(text):
        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p.paragraph_format.space_before = Pt(0)
        p.paragraph_format.space_after = Pt(20)
        run = p.add_run(text)
        run.font.size = Pt(13)
        run.font.italic = True
        run.font.color.rgb = RGBColor(0x55, 0x66, 0x77)
        return p

    def add_meta(data_dict):
        tbl = doc.add_table(rows=len(data_dict), cols=2)
        tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
        tbl.autofit = False
        set_table_borders(tbl, "CCCCCC")
        for i, (k, v) in enumerate(data_dict.items()):
            row = tbl.rows[i]
            c1, c2 = row.cells[0], row.cells[1]
            c1.width = Inches(2.2)
            c2.width = Inches(4.3)
            set_cell_background(c1, "F4F6F9")
            set_cell_background(c2, "FFFFFF")
            set_cell_margins(c1, 80, 80, 120, 120)
            set_cell_margins(c2, 80, 80, 120, 120)

            p1 = c1.paragraphs[0]
            p1.paragraph_format.space_after = Pt(0)
            r1 = p1.add_run(k)
            r1.bold = True
            r1.font.size = Pt(10)
            r1.font.color.rgb = RGBColor(0x0A, 0x45, 0x68)

            p2 = c2.paragraphs[0]
            p2.paragraph_format.space_after = Pt(0)
            r2 = p2.add_run(v)
            r2.font.size = Pt(10)
            r2.font.color.rgb = RGBColor(0x33, 0x33, 0x33)

        p = doc.add_paragraph()
        p.paragraph_format.space_after = Pt(24)

    def add_h1(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(20)
        p.paragraph_format.space_after = Pt(8)
        p.paragraph_format.keep_with_next = True
        run = p.add_run(text)
        run.bold = True
        run.font.size = Pt(16)
        run.font.color.rgb = RGBColor(0x0A, 0x45, 0x68)
        return p

    def add_h2(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(14)
        p.paragraph_format.space_after = Pt(6)
        p.paragraph_format.keep_with_next = True
        run = p.add_run(text)
        run.bold = True
        run.font.size = Pt(13)
        run.font.color.rgb = RGBColor(0x1B, 0x6B, 0x93)
        return p

    def add_h3(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(10)
        p.paragraph_format.space_after = Pt(4)
        p.paragraph_format.keep_with_next = True
        run = p.add_run(text)
        run.bold = True
        run.font.size = Pt(11.5)
        run.font.color.rgb = RGBColor(0x11, 0x7A, 0x8B)
        return p

    def add_p(text, bold_prefix=None):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(0)
        p.paragraph_format.space_after = Pt(6)
        p.paragraph_format.line_spacing = 1.15
        if bold_prefix:
            r_pre = p.add_run(bold_prefix)
            r_pre.bold = True
            r_pre.font.color.rgb = RGBColor(0x22, 0x22, 0x22)
        r = p.add_run(text)
        r.font.size = Pt(10.5)
        r.font.color.rgb = RGBColor(0x33, 0x33, 0x33)
        return p

    def add_bullet(text, bold_prefix=None):
        p = doc.add_paragraph(style='List Bullet')
        p.paragraph_format.space_before = Pt(0)
        p.paragraph_format.space_after = Pt(3)
        p.paragraph_format.line_spacing = 1.15
        if bold_prefix:
            r_pre = p.add_run(bold_prefix)
            r_pre.bold = True
            r_pre.font.color.rgb = RGBColor(0x22, 0x22, 0x22)
        r = p.add_run(text)
        r.font.size = Pt(10.5)
        r.font.color.rgb = RGBColor(0x33, 0x33, 0x33)
        return p

    def add_callout(text, title="TIPS PENGELOLAAN"):
        tbl = doc.add_table(rows=1, cols=1)
        tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
        tbl.autofit = False
        tbl.rows[0].cells[0].width = Inches(6.5)
        cell = tbl.rows[0].cells[0]
        set_cell_background(cell, "F0F7FB")
        set_callout_border(cell, "0A4568")
        set_cell_margins(cell, 100, 100, 160, 140)

        p = cell.paragraphs[0]
        p.paragraph_format.space_before = Pt(0)
        p.paragraph_format.space_after = Pt(3)
        r_title = p.add_run(f"📌 {title}: ")
        r_title.bold = True
        r_title.font.size = Pt(10)
        r_title.font.color.rgb = RGBColor(0x0A, 0x45, 0x68)

        r_text = p.add_run(text)
        r_text.font.size = Pt(10)
        r_text.font.color.rgb = RGBColor(0x22, 0x33, 0x44)

        p_space = doc.add_paragraph()
        p_space.paragraph_format.space_after = Pt(6)

    def add_table_data(headers, rows_data, col_widths=None):
        tbl = doc.add_table(rows=len(rows_data) + 1, cols=len(headers))
        tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
        tbl.autofit = False
        set_table_borders(tbl, "CCCCCC")

        # Header Row
        hdr_row = tbl.rows[0]
        for idx, h_text in enumerate(headers):
            cell = hdr_row.cells[idx]
            if col_widths and idx < len(col_widths):
                cell.width = Inches(col_widths[idx])
            set_cell_background(cell, "0A4568")
            set_cell_margins(cell, 100, 100, 120, 120)
            p = cell.paragraphs[0]
            p.paragraph_format.space_after = Pt(0)
            r = p.add_run(h_text)
            r.bold = True
            r.font.size = Pt(9.5)
            r.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)

        # Data Rows
        for r_idx, row_data in enumerate(rows_data):
            row = tbl.rows[r_idx + 1]
            bg_color = "F8FAFC" if r_idx % 2 == 1 else "FFFFFF"
            for c_idx, cell_value in enumerate(row_data):
                cell = row.cells[c_idx]
                if col_widths and c_idx < len(col_widths):
                    cell.width = Inches(col_widths[c_idx])
                set_cell_background(cell, bg_color)
                set_cell_margins(cell, 80, 80, 120, 120)
                p = cell.paragraphs[0]
                p.paragraph_format.space_after = Pt(0)
                r = p.add_run(str(cell_value))
                r.font.size = Pt(9.5)
                r.font.color.rgb = RGBColor(0x33, 0x33, 0x33)

        p_end = doc.add_paragraph()
        p_end.paragraph_format.space_after = Pt(8)

    # ==========================
    # COVER / HEADER SECTION
    # ==========================
    add_title("PANDUAN PENGGUNAAN WEBSITE & CONTENT MANAGEMENT SYSTEM (CMS)")
    add_subtitle("Buku Panduan Operasional Pengelolaan Konten Website Company Profile untuk Klien")

    meta_info = {
        "Nama Proyek": "Website Company Profile & Renewable Energy Solution",
        "Sasaran Pembaca": "Klien / Pemilik Usaha / Tim Administrator Website",
        "Framework & Platform": "Laravel 11, Blade Components & Bootstrap 5",
        "Hak Akses Panel": "Role Administrator (Halaman Admin CMS)",
        "Tanggal Rilis": "Oktober 2026",
        "Status Dokumen": "Final Release"
    }
    add_meta(meta_info)

    # ==========================
    # BAB 1: PENDAHULUAN
    # ==========================
    add_h1("BAB 1: PENDAHULUAN & KONSEP SISTEM")
    add_p(
        "Dokumen ini disusun sebagai panduan resmi dan praktis bagi Anda (klien/pemilik website) "
        "beserta tim pengelola konten dalam memahami seluruh fitur website company profile serta cara mengoperasikan "
        "panel Content Management System (CMS) admin. Seluruh konten dinamis yang tampil di hadapan pengunjung (frontend) "
        "dapat Anda perbarui kapan saja secara mandiri tanpa harus memahami bahasa pemrograman atau koding."
    )
    add_p(
        "Website ini dirancang secara khusus untuk menampilkan profil perusahaan energi baru dan terbarukan (renewable energy/solar PV) "
        "secara profesional, modern, responsif (nyaman diakses via smartphone, tablet, maupun laptop), "
        "dan ramah mesin pencari (SEO Optimized)."
    )

    add_h2("1.1 Keterhubungan Antara Frontend dan CMS Admin")
    add_p(
        "Website ini terbagi ke dalam dua antarmuka utama yang saling terhubung secara real-time:"
    )
    add_bullet(
        "Halaman Publik (Frontend): Halaman yang dapat diakses oleh masyarakat umum, calon klien, dan mitra bisnis melalui domain utama website.",
        "1. "
    )
    add_bullet(
        "Panel Administrator (CMS Admin): Halaman khusus berkeamanan tinggi yang hanya dapat diakses oleh tim Anda dengan akun login terdaftar (melalui URL /admin/login). Setiap kali data disimpan di admin, tampilan di frontend akan langsung terbarui seketika.",
        "2. "
    )

    add_callout(
        "Semua perubahan data (teks, nomor WhatsApp, foto, katalog produk, dokumen proposal PDF, dan logo klien) "
        "yang disimpan melalui Panel Admin akan langsung aktif seketika pada halaman publik tanpa perlu menunggu proses kompilasi ulang kode.",
        "INTEGRASI LANGSUNG"
    )

    # ==========================
    # BAB 2: FITUR HALAMAN FRONTEND
    # ==========================
    add_h1("BAB 2: JELAJAH LENGKAP FITUR HALAMAN FRONTEND (PENGUNJUNG)")
    add_p(
        "Berikut adalah penjelasan menyeluruh mengenai setiap menu dan fitur yang ada di halaman depan (frontend) website:"
    )

    # 2.1 HOME
    add_h2("2.1 Halaman Beranda (Home - /)")
    add_p(
        "Halaman utama berfungsi sebagai etalase terdepan untuk menarik perhatian pengunjung, membagikan identitas perusahaan, "
        "memberikan kalkulasi cepat manfaat energi surya, dan meyakinkan calon klien dengan rekam jejak nyata. Di dalamnya terdapat komponen-komponen berikut:"
    )
    add_bullet(
        "Menampilkan judul utama (headline), pengantar singkat, latar belakang gambar megah instalasi solar panel, serta dua tombol aksi cepat: 'About Us' dan 'Work with us' (langsung mengarah ke formulir kontak).",
        "Hero Banner & Tombol Utama: "
    )
    add_bullet(
        "Fitur interaktif tempat calon klien dapat memasukkan tagihan listrik bulanan (Rupiah) dan daya terpasang (Watt). Sistem secara otomatis menghitung estimasi kapasitas maksimum (kWp), luas area atap yang dibutuhkan (m²), nilai penghematan tagihan, serta sisa tagihan setelah menggunakan solar panel.",
        "Interactive Solar Calculator: "
    )
    add_bullet(
        "Ringkasan profil dan komitmen perusahaan dalam mendorong transisi energi hijau di Indonesia, dilengkapi foto instalasi rooftop industri berskala besar.",
        "Company Overview (Tentang Kami Singkat): "
    )
    add_bullet(
        "Slider logo berjalan tak terhingga (infinite marquee) yang menampilkan logo-logo perusahaan ternama yang telah mempercayakan proyek energinya kepada perusahaan Anda. Setiap logo dapat diklik untuk membuka situs resmi mitra.",
        "Trusted Companies (Slider Logo Klien): "
    )
    add_bullet(
        "Grid kartu keunggulan pemasangan solar panel, seperti penghematan biaya listrik, peningkatan nilai properti, pengurangan emisi karbon, dan keandalan sistem jangka panjang.",
        "Solar Benefits (Manfaat Tenaga Surya): "
    )
    add_bullet(
        "Visualisasi diagram dan penjelasan alur kerja sistem tenaga surya hybrid (PLN, solar panel, inverter, baterai cadangan) untuk menjamin pasokan listrik tanpa henti.",
        "Diagram Sistem Hybrid: "
    )
    add_bullet(
        "Menampilkan angka-angka pencapaian kunci (seperti '50+ MW Installed Capacity', '100+ Commercial Projects') guna menumbuhkan reputasi dan kepercayaan pengunjung.",
        "Our Impact in Numbers: "
    )
    add_bullet(
        "Kartu proyek unggulan lengkap dengan foto cover, kapasitas sistem (contoh: 405 kWp), lokasi proyek, dan tombol 'See more' yang mengarahkan ke katalog proyek lengkap.",
        "Featured Projects: "
    )
    add_bullet(
        "Slider testimoni interaktif berisi ulasan asli pelanggan/klien, lengkap dengan inisial avatar, nama, jabatan/perusahaan, rating bintang (1-5), dan kutipan kepuasan.",
        "Testimoni Klien: "
    )
    add_bullet(
        "Banner penutup di bagian bawah halaman beranda dengan ajakan kerja sama dan tombol direct untuk langsung memulai konsultasi.",
        "Contact CTA Banner: "
    )

    # 2.2 ABOUT US
    add_h2("2.2 Halaman Tentang Kami (About - /about)")
    add_p(
        "Halaman About dirancang untuk membangun kredibilitas korporat, memaparkan latar belakang bisnis, dan memperkenalkan sosok-sosok profesional di balik perusahaan:"
    )
    add_bullet(
        "Narasi sejarah perjalanan berdirinya perusahaan dari awal mula hingga berkembang menjadi penyedia solusi rekayasa energi terpercaya.",
        "Sejarah Perusahaan (Company History): "
    )
    add_bullet(
        "Penjabaran cita-cita jangka panjang (Visi) dan langkah-langkah strategis konkret (Misi) perusahaan dalam industri energi terbarukan.",
        "Visi & Misi Perusahaan: "
    )
    add_bullet(
        "Poin-poin bernomor yang menggarisbawahi kekuatan unik perusahaan (misalnya: tim engineer bersertifikasi, material berstandar Tier-1 internasional, dan dukungan purna jual prima).",
        "Mengapa Memilih Kami (Why Choose CDE): "
    )
    add_bullet(
        "Area kehormatan di bagian atas tim yang menampilkan foto resmi Chief Executive Officer (CEO), nama, jabatan, judul sambutan, serta pesan hangat dari pemimpin perusahaan.",
        "Pesan Resmi CEO (CEO Message): "
    )
    add_bullet(
        "Galeri kartu profil para profesional perusahaan yang memuat foto portrait berkualitas tinggi, nama lengkap, divisi/posisi, deskripsi keahlian, dan tautan sosial media resmi (LinkedIn, dll).",
        "Profil Anggota Tim (Meet Our Team): "
    )

    # 2.3 PRODUCT & SERVICE
    add_h2("2.3 Halaman Produk & Layanan (Product & Service - /product-services)")
    add_p(
        "Halaman ini langsung diakses ketika pengunjung mengeklik menu 'Product & Service' pada navigasi utama. "
        "Di halaman ini ditampilkan 2 kartu pilihan solusi utama:"
    )
    add_bullet(
        "Kartu Solar Component (Products): Saat diklik, pengunjung diarahkan ke halaman katalog lengkap (/product-services/products) yang dilengkapi fitur pencarian, filter kategori produk, halaman detail produk, dan tombol 'Contact Sales'.",
        "1. "
    )
    add_bullet(
        "Kartu Solar Services (Layanan Langsung Modal PDF): Pengunjung TIDAK dialihkan ke halaman detail lagi. Saat kartu 'Solar Services' diklik di mana saja, aplikasi langsung memunculkan Modal Pratinjau PDF di layar. Pengunjung dapat membaca proposal secara langsung di browser dan menekan tombol 'Download PDF' untuk menyimpannya ke perangkat mereka.",
        "2. "
    )
    add_p(
        "Fitur-Fitur Spesifik pada Menu Produk & Layanan:"
    )
    add_bullet(
        "Pengunjung dapat mencari nama produk, merk (brand), atau deskripsi pada bilah pencarian, serta menyaring katalog berdasarkan kategori produk (misal: String Inverter, Commercial Inverter, Residential).",
        "Pencarian & Filter Kategori Produk: "
    )
    add_bullet(
        "Memuat foto produk tajam, spesifikasi teknis, harga indikatif (jika diaktifkan), ringkasan produk, dan tombol khusus 'Contact Sales'.",
        "Halaman Detail Produk (/product-services/{slug}): "
    )
    add_bullet(
        "Formulir khusus bagi calon pembeli untuk menanyakan ketersediaan atau penawaran harga produk spesifik. Subjek pesan otomatis terisi sesuai nama produk yang diminati.",
        "Formulir 'Contact Sales' (/product-services/{slug}/contact-sales): "
    )
    add_bullet(
        "Setiap kartu layanan dilengkapi ikon visual, deskripsi ringkas, dan label 'Lihat & Download Proposal'. Saat kartu diklik di mana saja, sistem langsung memunculkan Modal Pratinjau PDF interaktif. Di dalam modal ini, pengunjung dapat membaca dokumen secara langsung serta menekan tombol 'Download PDF' untuk mengunduh file penawaran layanan.",
        "Kartu Layanan Interaktif & Modal PDF Proposal: "
    )

    # 2.4 PROJECTS
    add_h2("2.4 Halaman Portofolio Proyek (Project - /projects)")
    add_p(
        "Halaman portofolio membuktikan jam terbang dan keahlian nyata tim lapangan perusahaan kepada calon mitra besar:"
    )
    add_bullet(
        "Menampilkan seluruh proyek instalasi PLTS yang telah diselesaikan dalam tata letak kartu yang rapi, lengkap dengan nama proyek, kapasitas sistem, nama klien, dan lokasi.",
        "Katalog Daftar Proyek: "
    )
    add_bullet(
        "Menyajikan 3 foto dokumentasi lapangan beresolusi tinggi (Cover Utama, Foto Detail Utama, dan Foto Latar Belakang/Landscape), logo perusahaan klien, deskripsi proyek, spesifikasi daya (misal 405 kWp), lokasi geografis, dan overview pengerjaan.",
        "Halaman Detail Proyek (/projects/{slug}): "
    )

    # 2.5 MEDIA
    add_h2("2.5 Halaman Berita & Media (Media - /media)")
    add_p(
        "Halaman media berfungsi sebagai pusat informasi, artikel edukasi energi surya, dokumentasi kegiatan CSR, dan kabar terkini perusahaan:"
    )
    add_bullet(
        "Kumpulan artikel yang telah dipublikasikan dengan tampilan cover gambar, kategori berita, tanggal terbit, dan cuplikan singkat (excerpt). Dilengkapi navigasi halaman (pagination).",
        "Daftar Artikel & Berita: "
    )
    add_bullet(
        "Menampilkan artikel lengkap dengan format bacaan yang nyaman, tipografi terstruktur, foto cover besar, nama penulis, tanggal dan waktu terbit, serta konten kaya (rich text).",
        "Halaman Baca Detail Artikel (/media/{slug}): "
    )

    # 2.6 CONTACT & GLOBAL
    add_h2("2.6 Halaman Kontak & Elemen Global Website")
    add_bullet(
        "Menampilkan kartu informasi alamat kantor, alamat email resmi, nomor telepon, jam operasional, dan tautan langsung ke WhatsApp.",
        "Informasi Kontak (/contact): "
    )
    add_bullet(
        "Formulir online aman (dilengkapi sistem anti-spam honeypot dan proteksi batas pengiriman) tempat pengunjung dapat mengirimkan pertanyaan, nama, email, nomor HP, dan isi pesan.",
        "Formulir Kirim Pesan: "
    )
    add_bullet(
        "Peta Google Maps interaktif yang disematkan secara aman untuk mempermudah pengunjung menemukan lokasi fisik kantor perusahaan.",
        "Peta Lokasi Interaktif: "
    )
    add_bullet(
        "Memuat navigasi utama, logo perusahaan, dan tombol WhatsApp hijau mencolok di pojok kanan atas agar calon klien dapat berkonsultasi instan dengan 1 kali klik.",
        "Header Navigasi & Tombol Quick WhatsApp: "
    )
    add_bullet(
        "Memuat identitas singkat perusahaan, tautan halaman cepat, daftar lengkap ikon sosial media, dan teks hak cipta (copyright).",
        "Footer Website: "
    )

    # ==========================
    # BAB 3: PANDUAN PENGELOLAAN CMS ADMIN
    # ==========================
    add_h1("BAB 3: PANDUAN LENGKAP PENGELOLAAN PANEL CMS ADMIN")
    add_p(
        "Bab ini merupakan instruksi langkah demi langkah bagi tim pengelola dalam menggunakan panel admin CMS. "
        "Seluruh menu di panel admin dirancang dengan antarmuka yang bersih, intuitif, dan menggunakan bahasa yang mudah dipahami."
    )

    # 3.1 LOGIN
    add_h2("3.1 Cara Masuk (Login) dan Keluar (Logout) dari Panel Admin")
    add_bullet("Buka peramban (browser) seperti Google Chrome atau Microsoft Edge.", "Langkah 1: ")
    add_bullet("Ketikkan alamat URL panel admin: https://domainanda.com/admin/login (atau http://localhost/admin/login saat pengujian lokal).", "Langkah 2: ")
    add_bullet("Masukkan alamat email administrator dan kata sandi yang telah didaftarkan, lalu klik tombol 'Log In'.", "Langkah 3: ")
    add_bullet("Setelah berhasil, Anda akan langsung dialihkan ke halaman utama Dashboard Admin.", "Langkah 4: ")
    add_bullet("Untuk keluar, klik nama akun Anda di pojok kanan atas layar, lalu pilih opsi 'Logout'.", "Logout: ")

    # 3.2 DASHBOARD
    add_h2("3.2 Membaca Ringkasan Dashboard")
    add_p(
        "Pada Dashboard (/admin/dashboard), Anda disuguhkan kartu metrik utama:"
    )
    add_bullet("Products: Jumlah produk yang terdaftar dalam katalog.", "• ")
    add_bullet("Services: Jumlah layanan rekayasa yang aktif.", "• ")
    add_bullet("Projects: Total portofolio proyek yang ditampilkan.", "• ")
    add_bullet("Media Posts: Jumlah artikel/berita yang tersimpan.", "• ")
    add_bullet("New Messages: Jumlah pesan masuk dari pengunjung yang belum ditinjau.", "• ")
    add_bullet("Recent Activity: Catatan audit otomatis yang merekam riwayat penambahan, pengubahan, atau penghapusan konten oleh tim admin.", "• ")

    # 3.3 SITE SETTINGS
    add_h2("3.3 Mengelola Pengaturan Website (Menu: Site Settings)")
    add_p(
        "Menu Site Settings (/admin/settings) adalah pusat konfigurasi menyeluruh website Anda. "
        "Di sini Anda dapat mengubah informasi penting tanpa perlu menyentuh kode program:"
    )

    table_settings_headers = ["Bagian Pengaturan", "Nama Field / Input", "Fungsi & Tampilannya di Frontend"]
    table_settings_rows = [
        ["Brand Identity", "Company Name", "Nama resmi perusahaan yang tampil pada title tab browser, footer, dan teks copyright."],
        ["Brand Identity", "Short Name", "Nama singkatan perusahaan (misal: CDE) yang digunakan pada teks judul halaman."],
        ["Brand Identity", "Tagline", "Slogan perusahaan yang memperkuat citra brand."],
        ["Brand Identity", "Company Logo", "Upload file logo utama (format PNG transparan atau WEBP disarankan) untuk header dan footer."],
        ["Brand Identity", "Favicon", "Upload ikon kecil persegi (disarankan 32x32 px atau 64x64 px) yang tampil di tab browser."],
        ["Contact Info", "Public Email", "Alamat email resmi perusahaan yang ditampilkan di halaman kontak dan footer."],
        ["Contact Info", "Contact Recipient Email", "Alamat email tim internal yang dituju untuk menerima notifikasi pesan masuk."],
        ["Contact Info", "Phone Number", "Nomor telepon kabel/kantor yang dapat dihubungi."],
        ["Contact Info", "WhatsApp Number", "Nomor WhatsApp customer support (gunakan format internasional diawali 62, contoh: 628112257000)."],
        ["Contact Info", "WhatsApp Default Message", "Template pesan pembuka otomatis saat pengunjung mengklik tombol WhatsApp (contoh: 'Halo CDE, saya ingin konsultasi mengenai solar panel.')."],
        ["Contact Info", "Company Address", "Teks alamat fisik kantor yang tampil pada halaman kontak dan footer."],
        ["Google Maps", "Google Maps Embed URL", "URL embed resmi dari fitur 'Bagi / Sematkan Peta' pada Google Maps (masukkan isi atribut src-nya saja)."],
        ["Google Maps", "Open in Google Maps Link", "Tautan langsung untuk membuka lokasi kantor di aplikasi Google Maps pengunjung."],
        ["Social Media", "LinkedIn, IG, FB, X, YouTube, Telegram", "Tautan akun sosial media perusahaan. Kolom yang dibiarkan kosong otomatis tidak dimunculkan ikonnya di footer."],
        ["Footer & SEO", "Footer Copyright Text", "Teks pernyataan hak cipta di baris paling bawah halaman website."],
        ["Footer & SEO", "Default Meta Title & Desc", "Teks judul dan rangkuman deskripsi website yang dibaca oleh Google saat website dibagikan di internet."],
        ["Our Impact", "Number, Label, Order, Active", "Kelola angka pencapaian di halaman Beranda (contoh: '50+ MW' dengan label 'Installed Capacity'). Bisa tambah, urutkan, dan nonaktifkan."]
    ]
    add_table_data(table_settings_headers, table_settings_rows, [1.5, 1.8, 3.2])

    add_callout(
        "Setelah mengubah data pada Site Settings, pastikan Anda menekan tombol hijau 'Save Changes' di bagian bawah halaman. "
        "Sistem akan langsung memvalidasi input Anda dan menerapkan pembaruan ke seluruh halaman publik.",
        "PENTING"
    )

    # 3.4 PRODUCTS & SERVICES
    add_h2("3.4 Mengelola Produk & Layanan (Menu: Products & Services)")
    add_p(
        "Menu ini (/admin/services) mengelola dua jenis data bisnis Anda. Saat menekan tombol 'New Item', "
        "Anda dapat memilih jenisnya melalui dropdown **Type**:"
    )
    add_h3("A. Jika Memilih Type: Product")
    add_bullet("Name: Masukkan nama lengkap produk (contoh: 'HUAWEI Smart String Inverter SUN2000-50KTL').", "1. ")
    add_bullet("Slug: Biarkan kosong agar sistem otomatis membuat URL ramah mesin pencari dari nama produk.", "2. ")
    add_bullet("Brand: Merek pabrikan produk (contoh: Huawei, LONGi, Trina).", "3. ")
    add_bullet("Product Category: Pilih kategori produk (contoh: Commercial Inverter). Kelola pilihan kategori di menu Product Categories.", "4. ")
    add_bullet("Price & Currency: Masukkan nominal harga dan mata uang (contoh: IDR atau USD). Bisa disesuaikan jika ingin menampilkan harga.", "5. ")
    add_bullet("Main Image: Unggah foto produk berlatar belakang bersih (format JPG, PNG, atau WEBP, maks 2 MB).", "6. ")
    add_bullet("Deskripsi Singkat & Lengkap: Berikan penjelasan spesifikasi dan manfaat produk bagi konsumen.", "7. ")
    add_bullet("Active & Featured Switch: Aktifkan saklar 'Active on frontend' agar tampil, dan 'Featured' jika ingin diprioritaskan.", "8. ")

    add_h3("B. Jika Memilih Type: Service (Layanan Rekayasa)")
    add_bullet("Formulir Cerdas: Ketika memilih Type 'Service', sistem admin otomatis menyembunyikan kolom khusus produk (Brand, Kategori, Harga, dan Foto Produk) agar proses input ringkas dan bebas kesalahan.", "1. ")
    add_bullet("Name: Masukkan judul layanan (contoh: 'Engineering, Procurement & Construction (EPC)').", "2. ")
    add_bullet("Icon Class: Masukkan kelas icon Bootstrap (contoh: 'bi-tools', 'bi-shield-check', 'bi-lightning-charge').", "3. ")
    add_bullet("Deskripsi: Tuliskan ruang lingkup pekerjaan layanan rekayasa yang ditawarkan.", "4. ")
    add_bullet("Unggah Proposal (PDF): Unggah file dokumen proposal penawaran resmi (format PDF, maksimal 10 MB). File ini otomatis tampil di modal dan dapat diunduh oleh pengunjung saat kartu layanan diklik di frontend. Tersedia tombol 'Lihat PDF' dan 'Download PDF' di admin untuk menguji file.", "5. ")
    add_bullet("Indikator di Tabel Menu: Pada tabel daftar menu admin, kolom Price otomatis berubah menjadi status dokumen: badge hijau 'PDF Ready' jika file ada, atau badge abu-abu 'No PDF' jika belum diunggah.", "6. ")

    # 3.5 PROJECTS
    add_h2("3.5 Mengelola Portofolio Proyek (Menu: Projects)")
    add_p(
        "Menu Projects (/admin/projects) digunakan untuk menampilkan rekam jejak instalasi energi terbarukan yang telah Anda selesaikan:"
    )
    add_bullet("Project Title: Nama proyek (contoh: 'Rooftop Solar PV Installation 405 kWp').", "• ")
    add_bullet("Client Name: Nama perusahaan atau instansi pemesan proyek.", "• ")
    add_bullet("Capacity: Besaran kapasitas daya yang dibangun (contoh: '405 kWp' atau '1.2 MWp').", "• ")
    add_bullet("Category: Bidang proyek (contoh: 'Commercial & Industrial Rooftop').", "• ")
    add_bullet("Project Date: Tanggal penyelesaian proyek untuk pencatatan timeline.", "• ")
    add_bullet("Location: Kota / provinsi tempat instalasi berada (contoh: 'Cikarang, Jawa Barat').", "• ")
    add_bullet("Overview & Description: Penjelasan tantangan teknis, solusi rancangan sistem, dan hasil efisiensi yang dicapai klien.", "• ")
    add_bullet("Project Media (4 Aset Gambar):", "• ")
    add_bullet("Cover Image: Foto utama yang tampil pada kartu galeri katalog proyek.", "   - ")
    add_bullet("Main Detail Image: Foto resolusi tinggi yang tampil di bagian atas halaman detail proyek.", "   - ")
    add_bullet("Secondary Detail Image: Foto sudut pandang lain (wide-angle/aerial view) di bagian bawah galeri.", "   - ")
    add_bullet("Client Logo: Logo perusahaan klien untuk memberikan legitimasi bahwa proyek ini nyata dan terpercaya.", "   - ")

    # 3.6 CLIENT LOGOS
    add_h2("3.6 Mengelola Logo Mitra / Klien (Menu: Clients)")
    add_p(
        "Menu Clients (/admin/clients) mengatur deretan logo yang berjalan pada slider 'Trusted Companies' di Beranda:"
    )
    add_bullet("Klik 'Add Client' untuk mendaftarkan mitra baru.", "1. ")
    add_bullet("Client Name: Masukkan nama perusahaan mitra.", "2. ")
    add_bullet("Client Logo: Unggah file logo mitra (sangat disarankan memakai format PNG transparan agar tampil bersih di dalam lingkaran putih slider).", "3. ")
    add_bullet("Website URL: Tautan website resmi klien (jika diisi, pengunjung dapat mengeklik logo tersebut).", "4. ")
    add_bullet("Sort Order: Urutan kemunculan logo (angka 1 akan tampil lebih dulu dibanding angka 2).", "5. ")
    add_bullet("Active Switch: Matikan saklar jika Anda ingin menyembunyikan logo sementara waktu tanpa menghapusnya.", "6. ")

    # 3.7 TEAMS & CEO MESSAGE
    add_h2("3.7 Mengelola Tim & Pesan CEO (Menu: Teams)")
    add_p(
        "Menu Teams (/admin/teams) mengatur seksi 'Meet Our Team' di halaman Tentang Kami (/about). "
        "Terdapat dua cara penggunaan profil tim:"
    )
    add_h3("A. Profil Sambutan Khusus CEO (Direktur Utama)")
    add_bullet("Buat data tim baru atau edit profil direktur perusahaan.", "1. ")
    add_bullet("Centang saklar 'Display as CEO'.", "2. ")
    add_bullet("Isi field 'Message Title' (contoh: 'Message from Our CEO' atau 'Leading the Sustainable Future').", "3. ")
    add_bullet("Isi kolom 'CEO Message' dengan narasi sambutan resmi pemimpin perusahaan. Anda dapat memisahkan paragraf cukup dengan menekan tombol Enter dua kali.", "4. ")
    add_bullet("Unggah foto resmi CEO (portrait dengan komposisi wajah di tengah). Profil ini otomatis tampil menonjol di atas kartu anggota tim lainnya.", "5. ")

    add_h3("B. Profil Anggota Tim Biasa")
    add_bullet("Pastikan saklar 'Display as CEO' tidak dicentang.", "1. ")
    add_bullet("Isi Nama Lengkap, Jabatan (Position), dan Deskripsi Singkat keahlian/perannya di perusahaan.", "2. ")
    add_bullet("Unggah foto anggota tim (disarankan memiliki rasio dan gaya pencahayaan yang seragam).", "3. ")
    add_bullet("Tautan Sosial Media: Masukkan link profil LinkedIn, Instagram, atau platform profesional lainnya. Ikon sosial media akan otomatis muncul pada kartu profil yang bersangkutan.", "4. ")

    # 3.8 TESTIMONIALS
    add_h2("3.8 Mengelola Testimoni Klien (Menu: Testimonials)")
    add_p(
        "Menu Testimonials (/admin/testimonials) digunakan untuk membagikan kepuasan pengalaman klien di halaman Beranda:"
    )
    add_bullet("Name: Nama lengkap pemberi testimoni.", "• ")
    add_bullet("Initials: Inisial nama (contoh: 'EJ'). Jika Anda kosongkan, sistem akan otomatis meracik 2 huruf inisial dari nama yang dimasukkan.", "• ")
    add_bullet("Role / Company: Posisi jabatan dan instansi klien (contoh: 'Head of Facility Management, PT Industri Mandiri').", "• ")
    add_bullet("Rating: Pilih skor bintang dari 1 hingga 5.", "• ")
    add_bullet("Message (Quote): Kutipan kepuasan mengenai performa sistem surya, kecepatan pengerjaan, atau keramahan layanan purna jual.", "• ")

    # 3.9 MEDIA / ARTICLES
    add_h2("3.9 Menerbitkan Berita & Artikel Edukasi (Menu: Media)")
    add_p(
        "Menu Media (/admin/media) mengatur publikasi berita perusahaan dan artikel wawasan energi:"
    )
    add_bullet("Title: Judul artikel yang menarik dan informatif.", "• ")
    add_bullet("Category: Kategori tulisan (contoh: 'Industry News', 'CSR Update', 'Solar Insights').", "• ")
    add_bullet("Status: Pilih 'Draft' jika tulisan masih dalam penyusunan, atau 'Published' jika siap tayang.", "• ")
    add_bullet("Published At: Tentukan tanggal dan jam publikasi tulisan. Catatan: Artikel dengan status 'Published' hanya akan tampil di frontend jika waktu Published At telah terlewati (tidak berada di masa depan).", "• ")
    add_bullet("Cover Image: Unggah foto ilustrasi utama (rasio 16:9 disarankan, misal 1200x675 px).", "• ")
    add_bullet("Excerpt: Ringkasan 1-2 kalimat pengantar yang tampil di kartu daftar berita.", "• ")
    add_bullet("Content (Rich Text Editor): Tuliskan seluruh isi artikel. Anda dapat menggunakan format cetak tebal (bold), miring (italic), garis bawah, daftar bernomor, dan tautan link.", "• ")

    # 3.10 CONTACT MESSAGES
    add_h2("3.10 Meninjau Pesan Masuk dari Pengunjung (Menu: Contact Messages)")
    add_p(
        "Setiap kali ada pengunjung yang mengirim pesan melalui formulir kontak publik maupun formulir 'Contact Sales' pada katalog produk, "
        "data pesan tersebut akan langsung tersimpan secara aman di menu Contact Messages (/admin/contact-messages):"
    )
    add_bullet("Melihat Daftar: Menampilkan tabel pengirim, nomor HP, email, ringkasan pesan, serta tanggal pengiriman.", "• ")
    add_bullet("Pencarian: Anda dapat mencari pesan berdasarkan nama, email, nomor HP, atau kata kunci tertentu.", "• ")
    add_bullet("Detail Pesan: Klik tombol 'View' untuk membaca pesan lengkap, subjek, serta URL sumber pengiriman (misal: pengunjung menghubungi dari produk spesifik mana).", "• ")

    # ==========================
    # BAB 4: REKOMENDASI ASET & PRAKTIK TERBAIK
    # ==========================
    add_h1("BAB 4: STANDAR FORMAT ASET & TIPS OPTIMASI WEBSITE")
    add_p(
        "Agar website perusahaan Anda selalu memiliki kecepatan akses yang tinggi (ringan dibuka di smartphone) "
        "serta memiliki tampilan visual yang tajam dan profesional, berikut kami sajikan panduan standar aset:"
    )

    table_asset_headers = ["Jenis Aset", "Format File Rekomendasi", "Dimensi / Resolusi Ideal", "Batas Maksimal Ukuran"]
    table_asset_rows = [
        ["Logo Perusahaan", "PNG Transparan atau WEBP", "Tinggi 80 - 120 px (lebar proporsional)", "Maksimal 1 MB (ideal < 200 KB)"],
        ["Favicon", "PNG atau ICO", "32 x 32 px atau 64 x 64 px", "Maksimal 500 KB"],
        ["Hero Banner Utama", "WEBP atau JPG Terkompresi", "1920 x 1080 px (16:9 Landscape)", "Maksimal 2 MB (ideal < 500 KB)"],
        ["Foto Galeri Proyek", "JPG atau WEBP", "1200 x 800 px (3:2 Landscape)", "Maksimal 2 MB per foto"],
        ["Foto Cover Artikel", "JPG atau WEBP", "1200 x 675 px (16:9 Landscape)", "Maksimal 2 MB"],
        ["Foto Profil Tim / CEO", "JPG atau WEBP", "800 x 1000 px (Portrait 4:5)", "Maksimal 1.5 MB"],
        ["Logo Klien (Mitra)", "PNG Transparan", "Lebar 300 - 400 px (persegi/panjang)", "Maksimal 1 MB"],
        ["Dokumen Proposal", "PDF Resmi (Compressed)", "Dokumen standar A4", "Maksimal 10 MB"]
    ]
    add_table_data(table_asset_headers, table_asset_rows, [1.5, 1.8, 2.0, 1.2])

    add_h2("4.1 Tips Optimasi Mesin Pencari (Search Engine Optimization / SEO)")
    add_bullet(
        "Gunakan Judul Deskriptif: Saat mengisi judul proyek atau artikel, gunakan istilah yang sering dicari pengguna (contoh: 'Pemasangan Solar Panel Rooftop Pabrik di Cikarang').",
        "1. "
    )
    add_bullet(
        "Isi Alt Text Gambar: Setiap kali mengunggah gambar di CMS, tersedia kolom 'Image Alt Text'. Tuliskan deskripsi ringkas tentang foto tersebut agar mesin pencari Google dapat mengindeks foto Anda di Google Images.",
        "2. "
    )
    add_bullet(
        "Manfaatkan Excerpt: Buat ringkasan artikel antara 140 hingga 160 karakter agar kutipan yang muncul di pencarian Google tampak rapi dan memikat klik pembaca.",
        "3. "
    )

    add_h2("4.2 Checklist Sebelum Melakukan Publikasi")
    add_bullet("Periksa kejelasan teks dari kesalahan ketik (typo).", "☑ ")
    add_bullet("Pastikan nomor WhatsApp dan email pengelola sudah aktif dan dapat dihubungi.", "☑ ")
    add_bullet("Pastikan dokumen PDF proposal sudah bersih dari revisi internal sebelum diunggah ke publik.", "☑ ")
    add_bullet("Pastikan gambar tidak terbalik atau terdistorsi (gunakan gambar proporsional).", "☑ ")
    add_bullet("Gunakan tombol 'View Website' di panel admin untuk melihat langsung hasil tampilan di tab baru.", "☑ ")

    # Closing
    add_p(
        "Panduan ini dirancang untuk memudahkan operasional harian Anda. "
        "Jika Anda atau tim membutuhkan bantuan teknis lebih lanjut mengenai pemeliharaan server, pencadangan database, "
        "atau penambahan modul kustom di masa mendatang, silakan hubungi tim pengembang web Anda."
    )

    doc.save(output_path)
    print(f"Document successfully created at: {output_path}")

if __name__ == '__main__':
    target_path = os.path.abspath("Panduan_Penggunaan_CMS_Company_Profile.docx")
    build_documentation(target_path)

