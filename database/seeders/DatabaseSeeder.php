<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Faculties
        $teknik = Faculty::create(['name' => 'Teknik', 'level' => 'S1', 'code' => 'FT']);
        $ekonomi = Faculty::create(['name' => 'Ekonomi', 'level' => 'S1', 'code' => 'FE']);

        // 2. Seed Departments
        $informatika = Department::create(['faculty_id' => $teknik->id, 'name' => 'Informatika', 'level' => 'S1']);
        Department::create(['faculty_id' => $teknik->id, 'name' => 'Elektro', 'level' => 'S1']);
        Department::create(['faculty_id' => $ekonomi->id, 'name' => 'Akuntansi', 'level' => 'S1']);

        // 3. Seed Admin User
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@repo.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_verified' => true,
            'department_id' => $informatika->id,
        ]);

        // 4. Seed a Student for testing
        User::create([
            'name' => 'Mahasiswa Test',
            'email' => 'mahasiswa@test.id',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'is_verified' => true,
            'department_id' => $informatika->id,
            'nim' => '2024001',
        ]);
    }
}
