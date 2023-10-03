<?php

namespace Database\Seeders;

use App\Models\JobCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Language\Entities\Language;

class JobCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! config('zakirsoft.testing_mode')) {
            $categories = [
                'Engineer/Architects',
                'Garments/Textile',
                'Design/Creative',
                'Hospitality/ Travel/ Tourism',
                'IT & Telecommunication',
                'Medical/Pharma',
                'Driving/Motor Technician',
                'Law/Legal',
                'Others',
            ];
        } else {
            $categories = [
                'Engineer/Architects',
                'Garments/Textile',
            ];
        }

        $icons = [
            'fas fa-hammer',
            'fas fa-tshirt',
            'fas fa-pen',
            'fas fa-hospital',
            'fas fa-desktop',
            'fas fa-user-md',
            'fas fa-car',
            'fas fa-gavel',
            'fas fa-ellipsis-v',
        ];

        // foreach ($categories as $key => $category) {
        //     JobCategory::create([
        //         'name' => $category,
        //         'slug' => Str::slug($category),
        //         'image' => 'backend/image/default.png',
        //         'icon' => $icons[$key]
        //     ]);
        // }

        $languages = Language::all();

        foreach ($categories as $key => $data) {

            $translation = JobCategory::create([
                'image' => 'backend/image/default.png',
                'icon' => $icons[$key],
            ]);

            foreach ($languages as $language) {
                $translation->translateOrNew($language->code)->name = $data;
            }

            $translation->save();
        }
    }
}
