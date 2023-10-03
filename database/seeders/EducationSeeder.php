<?php

namespace Database\Seeders;

use App\Models\Education;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! config('zakirsoft.testing_mode')) {
            $educations = [
                'High School', 'Intermediate', 'Bachelor Degree', 'Master Degree', 'Graduated', 'PhD', 'Any',
            ];
        } else {
            $educations = [
                'Bachelor Degree', 'Graduated',
            ];
        }

        foreach ($educations as $education) {
            Education::create([
                'name' => $education,
                'slug' => Str::slug($education),
            ]);
        }
    }
}
