---
name: api-endpoint
description: Gunakan saat menambah atau mengubah pemanggilan endpoint backend Laravel (DTO, datasource, pemetaan error, interceptor Sanctum, pencatatan transaksi).
---

# API endpoint (Laravel + Sanctum)

## Langkah
1. Pastikan endpoint ada di kontrak API (OpenAPI/Scribe/file kontrak). Jangan menebak path atau
   bentuk JSON. Bila tidak ada, berhenti dan tanya.
2. Buat DTO `freezed` + `json_serializable` di `data/models`. Field nominal diparsing dengan
   `Money.parse` (skill `/money-handling`), bukan `double`.
3. Datasource memakai satu instance `dio` dari `core/network`. URL berasal dari konfigurasi
   flavor/`--dart-define`, tidak di-hardcode.
4. Header `Authorization: Bearer` ditambahkan oleh interceptor yang sudah ada, bukan per request.
   Token hanya dibaca dari `flutter_secure_storage`.
5. Petakan error di repository (layer `data`), bukan di UI:
   - 422 -> error validasi per field (baca `errors`)
   - 401/403 -> error autentikasi/otorisasi (401: hapus token, ke login; Sanctum tidak punya refresh token)
   - 429 -> retry dengan jeda
   - 5xx/timeout -> error jaringan/server
6. Untuk POST pencatatan transaksi: sertakan ID klien unik (UUID) agar retry tidak menggandakan data.
   Bila backend belum mendukung, tanya; jangan menyiasatinya diam-diam.
7. Jangan log body request/response yang berisi data finansial.
8. Tes datasource/repository dengan `mocktail`: sukses, 422, 401, 429, 5xx, dan timeout.

## Tanya dulu
Perubahan kontrak, alur login/logout, dan penyimpanan token.
