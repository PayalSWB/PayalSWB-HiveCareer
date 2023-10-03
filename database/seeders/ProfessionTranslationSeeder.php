<?php

namespace Database\Seeders;

use App\Models\Profession;
use App\Models\ProfessionTranslation;
use Illuminate\Database\Seeder;
use Modules\Language\Entities\Language;

class ProfessionTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = Language::all();

        $professions = Profession::all();
        if ($professions && count($professions) && count($professions) != 0) {
            foreach ($professions as $data) {
                foreach ($languages as $language) {
                    ProfessionTranslation::create([
                        'profession_id' => $data->id,
                        'locale' => $language->code,
                        'name' => $data->name ?? "{$language->code} name",
                    ]);
                }
            }
        }
    }
}
