---
name: new-feature
description: Gunakan saat membuat fitur baru di aplikasi Flutter (halaman, alur, atau modul baru) dengan struktur presentation/domain/data dan Riverpod.
---

# New feature

## Sebelum mulai
1. Baca AGENTS.md dan satu fitur yang sudah ada sebagai contoh gaya.
2. Pastikan kontrak API yang dibutuhkan terdokumentasi. Bila belum, berhenti dan tanya.
3. Tulis rencana singkat (file yang dibuat, risiko). Untuk fitur besar, minta konfirmasi.

## Langkah
1. Buat `lib/features/<fitur>/{presentation,domain,data}`.
2. **domain**: entity murni Dart, repository abstrak, use case bila ada logika. Tanpa impor `flutter`/`dio`.
   Nominal bertipe `Money` (lihat skill `/money-handling`).
3. **data**: DTO (`freezed` + `json_serializable`), datasource, implementasi repository.
   Untuk endpoint, ikuti skill `/api-endpoint`.
4. **presentation**: provider/notifier Riverpod, lalu halaman dan widget. Pecah bila `build()` > ~80 baris.
5. Teks lewat l10n. Navigasi lewat go_router.
6. Jalankan `dart run build_runner build --delete-conflicting-outputs`.
7. Tulis test sesuai skill `/write-tests`.
8. Selesaikan dengan skill `/verify-and-report`.

## Jangan
- Menambah package, mengubah `android/`/`ios/`, atau menyentuh alur autentikasi tanpa persetujuan.
- Memanggil dio atau repository langsung dari widget.
