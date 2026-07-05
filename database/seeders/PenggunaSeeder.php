<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $existingSuperadmin = Pengguna::where('email', 'superadmin@ruma.com')->first();
        
        if (!$existingSuperadmin) {
            // Buat pengguna superadmin
            Pengguna::create([
                'nama' => 'Super Admin',
                'email' => 'superadmin@ruma.com',
                'kata_sandi' => Hash::make('password'),
                'peran' => 'admin',
                'status_aktif' => true,
            ]);

            $this->command->info('Superadmin berhasil dibuat!');
            $this->command->info('Email: superadmin@ruma.com');
            $this->command->info('Password: password');
        } else {
            Pengguna::where('email', 'superadmin@ruma.com')->update(['peran' => 'admin']);
            $this->command->info('Superadmin sudah ada di database (peran diset ke admin).');
        }
    }
}
