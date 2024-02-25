<h1>CRUD Mahasiswa</h1>
<h3>Author: Rizki Kosasih</h3>

<p>#Requirement</p>
<ul>
  <li>PHP: ^8.1</li>
  <li>NodeJS: ^20.9.0</li>
</ul>

<p>#Instalasi</p>
<ul>
  <li>Buat Database baru dengan nama `dbMahasiswa` / jalankan database.sql yang tersedia pada folder ini</li>
  <li>Buka Terminal, lalu masuk ke project folder ini</li>
  <li>Ketikkan perintah cp .env.development .env</li>
  <li>Ketikkan composer install / composer update</li>
  <li>Setelah proses installasi package composer, Ketikkan yarn install / npm install</li>
  <li>Setelah proses installasi package node_modules, Ketikkan php artisan key:generate</li>
  <li>Ketikkan php artisan migrate:fresh <sub>*Migration Database</sub></li>
  <li>Ketikkan php artisan db:seeder --class=MahasiswaSeeder <sub>*Faker Data</sub></li>
  <li>Proses Installasi Selesai.</li>
</ul>

<p>#Running Aplikasi</p>
<ul>
  <li>Buka Terminal, lalu masuk ke project folder ini</li>
  <li>Ketikkan yarn build / npm run build</li>
  <li>Setelah proses build selesai, Ketikkan perintah php artisan serve</li>
  <li>Buka Browser dan masukkan url berikut: <a href="http://localhost:8000">http://localhost:8000</a></li>
</ul>

<p style="color:red;">
  #Note: <br/>
  Sebelum melakukan yarn build / npm build, Silahkan install package node_modules terlebih dahulu
</p>
