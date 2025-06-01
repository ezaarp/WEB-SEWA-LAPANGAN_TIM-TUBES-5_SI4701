<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Area;
use App\Models\FacilityType;
use App\Models\Facility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Users
        $admin = User::create([
            'name' => 'Admin Logistik',
            'email' => 'admin@telkomuniversity.ac.id',
            'password' => Hash::make('password'),
            'nim' => 'ADM001',
            'contact' => '081234567890',
            'role' => 'admin',
        ]);

        $penanggungjawab = User::create([
            'name' => 'Penanggung Jawab Fasilitas',
            'email' => 'pj@telkomuniversity.ac.id',
            'password' => Hash::make('password'),
            'nim' => 'PJ001',
            'contact' => '081234567891',
            'role' => 'penanggung_jawab',
        ]);

        $mahasiswa = User::create([
            'name' => 'John Doe',
            'email' => 'mahasiswa@student.telkomuniversity.ac.id',
            'password' => Hash::make('password'),
            'nim' => '1301210001',
            'contact' => '081234567892',
            'role' => 'mahasiswa',
        ]);

        // Create Areas
        $areas = [
            'Gedung A',
            'Gedung B', 
            'Gedung C',
            'Lapangan Olahraga',
            'Aula Utama',
            'Perpustakaan'
        ];

        foreach ($areas as $areaName) {
            Area::create(['name' => $areaName]);
        }

        // Create Facility Types
        $facilityTypes = [
            'Ruang Kelas',
            'Laboratorium Komputer',
            'Laboratorium Fisika',
            'Aula',
            'Lapangan Futsal',
            'Lapangan Basket',
            'Ruang Meeting',
            'Studio'
        ];

        foreach ($facilityTypes as $typeName) {
            FacilityType::create(['name' => $typeName]);
        }

        // Create Facilities
        $facilities = [
            // Gedung A
            ['name' => 'Ruang A101', 'area_id' => 1, 'facility_type_id' => 1, 'status' => 'available'],
            ['name' => 'Ruang A102', 'area_id' => 1, 'facility_type_id' => 1, 'status' => 'available'],
            ['name' => 'Lab Komputer A201', 'area_id' => 1, 'facility_type_id' => 2, 'status' => 'available'],
            
            // Gedung B
            ['name' => 'Ruang B101', 'area_id' => 2, 'facility_type_id' => 1, 'status' => 'available'],
            ['name' => 'Lab Fisika B201', 'area_id' => 2, 'facility_type_id' => 3, 'status' => 'available'],
            ['name' => 'Ruang Meeting B301', 'area_id' => 2, 'facility_type_id' => 7, 'status' => 'available'],
            
            // Gedung C
            ['name' => 'Studio C101', 'area_id' => 3, 'facility_type_id' => 8, 'status' => 'available'],
            ['name' => 'Ruang C201', 'area_id' => 3, 'facility_type_id' => 1, 'status' => 'unavailable'],
            
            // Lapangan Olahraga
            ['name' => 'Lapangan Futsal 1', 'area_id' => 4, 'facility_type_id' => 5, 'status' => 'available'],
            ['name' => 'Lapangan Basket 1', 'area_id' => 4, 'facility_type_id' => 6, 'status' => 'available'],
            
            // Aula Utama
            ['name' => 'Aula Besar', 'area_id' => 5, 'facility_type_id' => 4, 'status' => 'available'],
            ['name' => 'Aula Kecil', 'area_id' => 5, 'facility_type_id' => 4, 'status' => 'available'],
        ];

        foreach ($facilities as $facility) {
            Facility::create($facility);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@telkomuniversity.ac.id / password');
        $this->command->info('Penanggung Jawab: pj@telkomuniversity.ac.id / password');
        $this->command->info('Mahasiswa: mahasiswa@student.telkomuniversity.ac.id / password');
    }
}
