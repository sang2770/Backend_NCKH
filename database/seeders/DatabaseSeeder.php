<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(KhoaSeeder::class);
        $this->call(ClassSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(TKSVSeeder::class);
        $this->call(TaiKhoanSeeder::class);
        $this->call(YeuCauSeeder::class);
        $this->call(TrangThaiSVSeeder::class);
        $this->call(GiayCNDKSeeder::class);
        $this->call(DC_DiaPhuongSeeder::class);
        $this->call(GiayDCTruongSeeder::class);
        $this->call(ThongBaoChinhSeeder::class);
    }
}
