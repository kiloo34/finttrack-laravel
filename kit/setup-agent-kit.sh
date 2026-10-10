#!/usr/bin/env bash
# Jalankan dari root proyek Flutter: bash setup-agent-kit.sh
set -euo pipefail
mkdir -p .claude
if [ -e .claude/skills ] && [ ! -L .claude/skills ]; then
  echo "Peringatan: .claude/skills sudah ada dan bukan symlink. Pindahkan isinya ke .agents/skills dulu." >&2
  exit 1
fi
ln -sfn ../.agents/skills .claude/skills
echo "OK: .claude/skills -> ../.agents/skills"
ls -l .claude/skills
