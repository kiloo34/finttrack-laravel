---
name: verify-and-report
description: Gunakan sebelum menyatakan tugas selesai atau membuat PR: menjalankan Definition of Done lalu menulis laporan perubahan, pengujian, dan risiko.
---

# Verify and report

## Langkah
1. Jalankan:
   ```bash
   dart format --set-exit-if-changed .
   flutter analyze --fatal-infos
   flutter test --coverage
   ```
2. Periksa diff secara manual:
   ```bash
   git diff --name-only
   git diff | grep -nE "\bdouble\b" || true      # nominal tidak boleh double
   git diff | grep -nE "print\(|ignore_for_file|// ignore:" || true
   ```
   Pastikan tidak ada secret/token, tidak ada data finansial di log, dan file generate sinkron.
3. Cek apakah diff menyentuh area "tanya dulu" (android/, ios/, pubspec.yaml, kontrak API, rumus saldo).
   Bila ya, tandai dan minta persetujuan.
4. Tulis laporan:

```
## Ringkasan
(apa yang berubah dan kenapa)

## Pengujian
(perintah yang dijalankan dan hasilnya; apa yang TIDAK bisa diuji)

## Risiko
(area berisiko, asumsi, hal yang perlu ditinjau manusia)
```

## Aturan
- Jangan menulis "sudah berfungsi" bila perintah di atas belum dijalankan atau gagal.
- Jika ada langkah yang tidak bisa dijalankan (mis. tidak ada emulator), nyatakan terang-terangan.
