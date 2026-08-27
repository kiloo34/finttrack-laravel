# Git & CI/CD Workflow Documentation

Dokumen ini menjelaskan alur kerja (workflow) Git standar yang digunakan dalam proyek ini, mulai dari pengembangan fitur baru hingga perilisan ke versi stabil (Production). Workflow ini dirancang untuk mendukung metodologi Scrum dan terintegrasi dengan GitLab CI/CD.

## 1. Struktur Branch (Branching Strategy)

Proyek ini menggunakan struktur branch berikut:
- **`main`**: Versi stabil (Production). Kode di sini harus selalu siap rilis dan bebas error.
- **`dev`**: Versi integrasi (Staging/Development). Semua fitur baru yang telah selesai dalam sebuah Sprint digabungkan ke sini.
- **`feature/*`**: Digunakan untuk mengembangkan fitur baru (contoh: `feature/FIN-12-login`).
- **`bugfix/*`**: Digunakan untuk memperbaiki bug selama Sprint berjalan (contoh: `bugfix/FIN-34-fix-balance`).
- **`hotfix/*`**: Digunakan untuk perbaikan darurat di Production (bercabang dari `main`).
- **`chore/*`**: Digunakan untuk pekerjaan teknis non-fitur, seperti update versi PHP atau library.

---

## 2. Alur Pengembangan Fitur (Feature Development)

### Langkah 1: Mempersiapkan Lingkungan Lokal
Pastikan Anda berada di branch `dev` terbaru sebelum memulai fitur baru.

```bash
# Pindah ke branch integrasi (dev)
git checkout dev

# Tarik perubahan terbaru dari server
git pull origin dev
```

### Langkah 2: Membuat Branch Baru
Buat branch baru sesuai dengan tiket Scrum yang Anda kerjakan. Gunakan format `tipe/ID-TIKET-deskripsi`.

```bash
# Membuat branch baru dan otomatis berpindah ke branch tersebut
git checkout -b feature/FIN-100-modul-akun
```

### Langkah 3: Pengembangan dan Pengujian (Coding & Testing)
Lakukan perubahan kode sesuai kebutuhan fitur. Setelah selesai, selalu jalankan pengujian (Unit Test) di lokal Anda.

```bash
# Jalankan Pest (PHP Testing Framework)
vendor/bin/pest
```
> [!IMPORTANT]
> Jangan lakukan *commit* jika terdapat *test* yang *failed* (gagal). Perbaiki error tersebut di lokal Anda terlebih dahulu.

### Langkah 4: Menyimpan Perubahan (Commit)
Gunakan format **Conventional Commits** (`feat:`, `fix:`, `chore:`, dll) agar riwayat log Git mudah dibaca.

```bash
# Tambahkan file yang berubah ke staging area
git add .

# Simpan perubahan (commit)
git commit -m "feat(account): implementasi fitur kalkulasi saldo awal"
```

### Langkah 5: Mengirim Kode ke GitLab (Push)
Kirim (push) branch fitur Anda ke server GitLab.

```bash
git push origin feature/FIN-100-modul-akun
```

---

## 3. Proses CI/CD & Merge Request

Setelah perintah `git push` dijalankan pada branch `feature/*`, Pipeline GitLab CI akan terpicu secara otomatis:

1. **Test Stage**: GitLab akan menjalankan *Unit Test*.
2. **Create-MR Stage**: Jika semua test **Passed** (berhasil), GitLab akan otomatis membuat *Merge Request* (MR) dari branch `feature/...` Anda ke branch `dev`.

### Code Review
- Buka dashboard GitLab Anda.
- Mintalah rekan setim atau Tech Lead Anda untuk meninjau (Review) *Merge Request* tersebut.
- Jika ada revisi, cukup lakukan perbaikan di lokal, lalu `git commit` dan `git push` lagi. MR akan otomatis diperbarui dan Unit Test akan dijalankan ulang.

### Merge ke Dev
- Jika *Code Review* disetujui (Approved), klik tombol **Merge** di GitLab.
- Sekarang fitur Anda sudah tergabung di branch `dev` dan bisa digunakan oleh tim lain.

---

## 4. Merilis ke Versi Stabil (Release to Main via QA)

Berdasarkan arsitektur *Continuous Deployment* kita, proses merge ke `qa` dan `main` sudah **diotomatisasi** via GitLab CI:

1. **Auto-Merge Fitur ke `qa`:** Setiap kali branch fitur Anda di-merge ke `dev`, GitLab CI di branch `dev` akan otomatis mendeteksi nama branch fitur asli Anda, lalu membuat Merge Request dari branch fitur tersebut ke branch `qa` dan langsung menerimanya (*Auto-Merge*). Ini memastikan `qa` terisolasi dan hanya menerima fitur yang disetujui (bukan seluruh *history* dari `dev`).
2. **Scheduled-Merge `qa` ke `main`:** Setiap seminggu sekali (berdasarkan jadwal yang Anda konfigurasi di GitLab Pipeline Schedules), CI akan mengambil seluruh sekumpulan fitur dari `qa` dan otomatis me-mergenya ke `main` (Production).

> [!NOTE]
> *Developer* tidak perlu melakukan operasi *merge* ke `qa` atau `main` secara manual di terminal. Cukup fokus pada penyelesaian fitur di branch Anda dan melakukan *merge* ke `dev`.

---

## 5. Penanganan Darurat (Hotfix di Production)

Jika terdapat *bug* kritis di Production (`main`), perbaikan tidak boleh melalui `dev`, melainkan langsung dari `main`:

```bash
# 1. Mulai dari main yang stabil
git checkout main
git pull origin main

# 2. Buat branch hotfix
git checkout -b hotfix/FIN-999-fix-critical-crash

# 3. Perbaiki bug, lalu commit dan push
git add .
git commit -m "fix(core): perbaiki aplikasi crash saat login"
git push origin hotfix/FIN-999-fix-critical-crash
```

Setelah selesai, *Merge Request* dari `hotfix/...` harus di-merge ke **`main`** DAN **`dev`** agar perbaikan tersebut tidak hilang saat rilis Sprint berikutnya.
