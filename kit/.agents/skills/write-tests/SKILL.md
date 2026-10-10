---
name: write-tests
description: Gunakan saat menulis atau melengkapi test (unit, widget) untuk logika baru, terutama perhitungan uang, repository, dan notifier Riverpod.
---

# Write tests

## Prioritas (dari yang paling penting)
1. `core/money` dan logika domain (saldo, total, laporan): target coverage 90%+, termasuk kasus batas.
2. Repository dan pemetaan error (mock `dio` dengan `mocktail`).
3. Notifier/controller Riverpod (gunakan `ProviderContainer` dengan override).
4. Widget test untuk layar penting: loading, data, kosong, error.

## Aturan
- Satu perilaku per test; nama test menjelaskan perilaku, bukan nama method.
- Gunakan data uji berupa tabel (daftar kasus) untuk perhitungan.
- Jangan memanggil jaringan atau backend sungguhan di test.
- Jangan menghapus, melonggarkan, atau melewati (`skip`) test agar pipeline lulus.
  Bila test gagal karena perilaku memang berubah, jelaskan di laporan dan tanya.
- Nominal di test memakai `Money`, bukan `double`.

## Selesai bila
`flutter test --coverage` lulus dan logika baru tercakup.
