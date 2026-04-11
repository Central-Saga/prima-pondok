#!/bin/bash

echo "🚀 Memulai proses setup project..."

# Copy .env jika belum ada
if [ ! -f .env ]; then
    echo "📄 Menyalin .env.example menjadi .env..."
    cp .env.example .env
else
    echo "✅ File .env sudah ada."
fi

echo "📁 Membuat direktori storage yang dibutuhkan..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p bootstrap/cache

echo "📦 Menjalankan composer install..."
composer install

echo "📦 Menjalankan npm install..."
npm install

echo "🔑 Men-generate application key..."
php artisan key:generate

echo "🗄️ Menjalankan database migration..."
# Tambahkan --force agar tidak meminta konfirmasi di environment production (opsional)
php artisan migrate --force

echo "🔗 Membuat storage link..."
php artisan storage:link

echo "🧹 Membersihkan semua cache..."
php artisan optimize:clear

echo "🏗️ Mem-build frontend assets (npm run build)..."
npm run build

echo "✅ Setup project selesai! Anda sudah bisa menjalankan project ini."
