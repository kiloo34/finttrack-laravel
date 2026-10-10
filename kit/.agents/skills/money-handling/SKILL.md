---
name: money-handling
description: Gunakan saat menulis atau mengubah kode yang menyentuh nominal uang, saldo, total, pembulatan, format mata uang, atau parsing nominal dari JSON API Laravel.
---

# Money handling

Aturan dasarnya ada di AGENTS.md (bagian "Aturan uang"). Skill ini adalah prosedurnya.

## Langkah
1. Cek apakah `lib/core/money/money.dart` sudah ada. Bila ada, pakai; jangan buat tipe uang baru.
2. Semua nominal di domain dan presentation bertipe `Money`, bukan `int`/`double` mentah.
3. Parsing dari JSON hanya di layer `data` lewat `Money.parse`. Nominal bisa datang sebagai
   `int`, `String` ("150000.00"), atau angka JSON yang terbaca sebagai `double` (150000.0).
4. Pembulatan, penjumlahan, dan pembagian hanya lewat method `Money`. Pembagian/persentase
   harus menyatakan aturan pembulatannya secara eksplisit.
5. Format tampilan lewat `intl` dengan locale (mis. `id_ID`); jangan merangkai string manual.
6. Pastikan tidak ada nominal di log atau pesan error.
7. Tulis test (lihat daftar di bawah) sebelum menyatakan selesai.

## Pola referensi (sesuaikan dengan proyek)
```dart
@immutable
class Money {
  const Money(this.minor); // satuan terkecil (rupiah penuh)
  final int minor;

  /// Menerima int, String desimal bernilai bulat ("150000.00"), atau double bernilai bulat.
  /// Menolak nilai pecahan yang tak bisa dinyatakan dalam satuan terkecil.
  factory Money.parse(Object? raw) {
    if (raw is int) return Money(raw);
    if (raw is String) {
      final m = RegExp(r'^(-?\d+)(?:\.0+)?$').firstMatch(raw.trim());
      if (m == null) throw FormatException('Nominal tidak valid', raw);
      return Money(int.parse(m.group(1)!));
    }
    if (raw is double && raw.isFinite && raw == raw.truncateToDouble()) {
      return Money(raw.toInt());
    }
    throw FormatException('Nominal tidak valid', raw);
  }

  Money operator +(Money o) => Money(minor + o.minor);
  Money operator -(Money o) => Money(minor - o.minor);
}
```
Jika aplikasi perlu sen/pecahan, ganti `int` dengan `Decimal`, jangan `double`. Tanyakan dulu.

## Test wajib
- nol, negatif, dan nilai sangat besar
- `"150000.00"`, `"150000"`, `150000`, `150000.0`
- input tidak valid: `"abc"`, `"150000.50"` (pecahan), `null`, `NaN`
- penjumlahan/pengurangan berantai tidak bergeser
- format `id_ID`
