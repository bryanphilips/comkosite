#!/usr/bin/env bash
set -e
IMG_DIR="$(cd "$(dirname "$0")/../assets/img" && pwd)"
echo "Converting JPG/PNG to WebP + AVIF in: $IMG_DIR"
shopt -s nullglob
for f in "$IMG_DIR"/*.{jpg,JPG,jpeg,JPEG,png,PNG}; do
  base="${f%.*}"
  if command -v cwebp >/dev/null 2>&1; then
    cwebp -q 82 "$f" -o "${base}.webp"
  else
    echo "cwebp not found; install libwebp"
  fi
  if command -v avifenc >/dev/null 2>&1; then
    avifenc --min 20 --max 28 "$f" "${base}.avif"
  else
    echo "avifenc not found; install libavif"
  fi
done
echo "Done."