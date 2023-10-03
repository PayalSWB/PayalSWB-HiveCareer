<?php

namespace Database\Seeders;

use App\Models\OrganizationType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Language\Entities\Language;

class OrganizationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! config('zakirsoft.testing_mode')) {
            $types = [
                'Government', 'Semi Government', 'Public', 'Private', 'NGO', 'International Agencies',
            ];
        } else {
            $types = [
                'Government', 'Semi Government',
            ];
        }

        // foreach ($types as $type) {
        //     OrganizationType::create([
        //         'name' => $type,
        //         'slug' => Str::slug($type)
        //     ]);
        // }

        $languages = Language::all();

        foreach ($types as $data) {
            $translation = new OrganizationType();
            $translation->save();

            foreach ($languages as $language) {
                $translation->translateOrNew($language->code)->name = $data;
            }

            $translation->save();
        }
    }
}
