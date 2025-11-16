<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'admin', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'user',  'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
