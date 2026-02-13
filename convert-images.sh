#!/usr/bin/env bash
set -euo pipefail

IN_DIR="assets/img"
OUT_WEBP="${IN_DIR}"
OUT_AVIF="${IN_DIR}"

mkdir -p "$OUT_WEBP" "$OUT_AVIF"

shopt -s nullglob nocaseglob

# ---------- Convert JPG/JPEG to WebP + AVIF ----------
jpg_files=("$IN_DIR"/*.jpg "$IN_DIR"/*.jpeg)

if [ ${#jpg_files[@]} -gt 0 ]; then
  WEBP_Q=82
  AVIF_Q=35
  AVIF_SPEED=6

  for f in "${jpg_files[@]}"; do
    base="$(basename "$f")"
    name="${base%.*}"

    echo "Converting: $base"
    cwebp -q "$WEBP_Q" "$f" -o "${OUT_WEBP}/${name}.webp" >/dev/null
    avifenc -q "$AVIF_Q" -s "$AVIF_SPEED" "$f" "${OUT_AVIF}/${name}.avif" >/dev/null
  done
else
  echo "No .jpg/.jpeg in $IN_DIR to convert."
fi

# ---------- SAFE DELETE (moves originals into trash folder) ----------
# This will move ALL .png/.jpg/.jpeg in comkimages/ and its subfolders,
# BUT will NOT touch comkimages/webp or comkimages/avif.
TRASH_DIR="${IN_DIR}/_deleted_originals_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$TRASH_DIR"

echo "Safely moving originals (.png/.jpg/.jpeg) into: $TRASH_DIR"
echo "Excluded folders: $OUT_WEBP and $OUT_AVIF"

# Move matching files while preserving relative paths
find "$IN_DIR" -type f \( -iname "*.png" -o -iname "*.jpg" -o -iname "*.jpeg" \) \
  ! -path "$OUT_WEBP/*" \
  ! -path "$OUT_AVIF/*" \
  -print0 |
while IFS= read -r -d '' file; do
  rel="${file#"$IN_DIR"/}"
  dest_dir="$TRASH_DIR/$(dirname "$rel")"
  mkdir -p "$dest_dir"
  mv -f "$file" "$dest_dir/"
done

echo "Done."
echo "WebP:  $OUT_WEBP"
echo "AVIF:  $OUT_AVIF"
echo "Moved originals to: $TRASH_DIR"