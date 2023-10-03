<?php

namespace Database\Seeders;

use App\Models\SalaryType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SalaryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'Monthly', 'Project Basis', 'Hourly', 'Yearly',
        ];

        foreach ($types as $type) {
            SalaryType::create([
                'name' => $type,
                'slug' => Str::slug($type),
            ]);
        }
    }
}
