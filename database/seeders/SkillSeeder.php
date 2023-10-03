<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Modules\Language\Entities\Language;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $skills = collect(require storage_path('skills.php'));
        $languages = Language::all();

        foreach ($skills as $data) {
            $translation = new Skill();
            $translation->save();

            foreach ($languages as $language) {
                $translation->translateOrNew($language->code)->name = $data;
            }

            $translation->save();
        }
    }
}
