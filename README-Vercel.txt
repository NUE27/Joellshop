JOELL SHOP — PAKET VERCEL SIAP DEPLOY

Paket ini khusus untuk Vercel. Jangan mencampurnya dengan paket PHP atau folder proyek Manus.

LANGKAH DEPLOY
1. Upload folder ini ke repository GitHub baru atau deploy langsung lewat Vercel.
2. Di Vercel buka Project Settings > Environment Variables.
3. Tambahkan variable:
   Name: LZPEDIA_API_KEY
   Value: API key LZ Pedia terbaru
   Environment: Production, Preview, Development
4. Tambahkan variable kedua:
   Name: LZPEDIA_BASE_URL
   Value: https://app.lzpedia.my.id/api
5. Redeploy project setelah menyimpan variables.
6. Buka website di root dan panel admin melalui /admin.

STRUKTUR PENTING
- index.html: storefront
- admin.html: panel admin
- api/lzpedia.js: proxy pembayaran server-side
- payment-api.js: pemanggil proxy dari browser
- vercel.json: routing /admin, /pesanan, dan /profil

PEMBAYARAN
Browser hanya memanggil /api/lzpedia. API key dibaca oleh api/lzpedia.js dari process.env.LZPEDIA_API_KEY dan tidak dikirim sebagai kode frontend. QR yang tampil hanya QR dari respons gateway LZ Pedia.

JANGAN lakukan hal berikut:
- Jangan menaruh API key di script.js, payment-api.js, atau index.html.
- Jangan meng-upload lzpedia-proxy.php ke paket Vercel ini.
- Jangan memakai deployment lama yang masih menampilkan pesan api/lzpedia.js belum di-deploy.

PANEL ADMIN
Buka /admin. Password sesuai konfigurasi project saat ini: X.
