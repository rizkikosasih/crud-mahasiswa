<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MahasiswaSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $faker = Factory::create('id_ID');
    DB::table('mahasiswa')->delete();
    DB::table('mhs_to_matkul')->delete();
    for ($i = 1; $i <= 15; $i++) {
      $name = $i % 2 === 0 ? 'male' : 'female';
      $jenis_kelamin = $i % 2 === 0 ? 'pria' : 'wanita';
      DB::table('mahasiswa')->insert([
        'id' => $i,
        'nama' => $faker->name($name),
        'alamat' => $faker->address(),
        'jenis_kelamin' => $jenis_kelamin,
        'created_at' => $faker->dateTimeBetween('-1 Month'),
        'updated_at' => now(),
      ]);

      for ($k = 1; $k <= rand(1, 10); $k++) {
        DB::table('mhs_to_matkul')->insert([
          'mahasiswa_id' => $i,
          'nama_matkul' => $faker->words('2', true),
          'created_at' => $faker->dateTimeBetween('-1 Month'),
          'updated_at' => now(),
        ]);
      }
    }
  }
}
