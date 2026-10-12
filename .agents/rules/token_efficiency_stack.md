---
trigger: always_on
description: Token Efficiency Stack combining RTK (Terminal), Ponytail (Codebase), and Caveman (Conversation) boundaries plus Subagent Isolation.
---

# Token Efficiency Stack (RTK + Ponytail + Caveman)

Aturan ini wajib dipatuhi oleh Agent utama maupun seluruh Subagent di setiap langkah pengerjaan untuk meminimalkan konsumsi token tanpa mengorbankan akurasi logika dan kualitas kode.

---

## 1. Terminal Boundary — RTK (Rust Token Killer)
Saat menjalankan perintah shell yang berpotensi menghasilkan log panjang (`git`, `test`, `grep`, `find`, `npm`, `composer`, `flutter`):
- Gunakan `/opt/homebrew/bin/rtk <command>` (jika tersedia di sistem) untuk mengompresi output terminal sebelum masuk ke context window.
- Contoh:
  - `rtk git status` / `rtk git diff` / `rtk git log -n 5`
  - `rtk php artisan test`
  - `rtk /Users/robileksono/Flutter/flutter/bin/flutter test`
  - `rtk /Users/robileksono/Flutter/flutter/bin/flutter analyze`
- Jika perintah tidak memerlukan output penuh, batasi baris dengan `--compact`, `-q`, atau `head -n 30`.
- Hindari melakukan grep tanpa `--exclude-dir={vendor,node_modules,.git,build,.dart_tool}`.

---

## 2. Codebase Boundary — Ponytail (Minimum Sufficient Code)
Cegah pembengkakan codebase (*anti-overengineering*):
- **Minimum Sufficient Code:** Tulis kode sesederhana dan setipis mungkin yang memenuhi requirement bisnis & lulus pengujian.
- **No Speculative Abstractions:** Jangan membuat `Interface`, `Factory`, `Manager`, `Strategy`, atau *wrapper class* tambahan jika hanya ada 1 implementasi nyata dan tidak diminta oleh arsitektur modul.
- **Targeted File Reading:** Jangan membaca keseluruhan isi file besar (>200 baris) sekaligus; gunakan `StartLine` dan `EndLine` hanya pada bagian yang relevan.
- **Atomic Edits:** Gunakan `replace_file_content` hanya pada blok baris yang berubah, jangan menimpa ulang seluruh file.

---

## 3. Conversation Boundary — Caveman (Terse Output)
Hemat *output tokens* pada setiap respons percakapan dan laporan subagent:
- **No Fluff / No Preamble:** Dilarang membuka atau menutup respons dengan kalimat basa-basi (*"Tentu, saya akan membantu Anda..."*, *"Semoga ini membantu!"*, dll.).
- **Bullet & Diff First:** Gunakan poin-poin super padat (*telegram/caveman-lite style*): sebutkan langsung **Status**, **File yang diubah (dengan link)**, dan **Hasil Verifikasi/Command**.
- **Preserve Deep Thinking for Code Only:** Penghematan kata hanya berlaku pada teks percakapan/output, **bukan** pada akurasi kode, validasi keamanan, atau cakupan pengujian (*test coverage*).

---

## 4. Context Boundary — Subagent Atomic Isolation
- Untuk tugas multi-file atau pembuatan fitur baru, pecah menjadi *atomic tasks* dan delegasikan ke `invoke_subagent` agar ribuan token pembacaan file dan iterasi test terisolasi di context subagent.
- Subagent wajib membalas hanya dengan laporan ringkas maksimal 10–15 baris poin inti.

