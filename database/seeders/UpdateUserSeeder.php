<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UpdateUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['id' => 3],
            [
                'name' => 'YJ Finance',
                'email' => 'yjs_finance@gmail.com',
                'password' => Hash::make('finance123'),
            ]
        );
    }
}
