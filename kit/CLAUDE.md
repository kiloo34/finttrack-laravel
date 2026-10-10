@AGENTS.md

# Catatan khusus Claude Code
- Skill ada di `.agents/skills/` (di-symlink ke `.claude/skills/`). Panggil dengan `/nama-skill`.
- Agent: `@flutter-dev` (pelaksana) dan `@reviewer` (read-only). Definisinya di `.claude/agents/`.
- Aturan hanya ditulis di `AGENTS.md`. Jangan menambah aturan di file ini.
