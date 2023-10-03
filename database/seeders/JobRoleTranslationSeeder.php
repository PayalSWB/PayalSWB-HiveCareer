<?php

namespace Database\Seeders;

use App\Models\JobRole;
use App\Models\JobRoleTranslation;
use Illuminate\Database\Seeder;
use Modules\Language\Entities\Language;

class JobRoleTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = Language::all();

        $roles = JobRole::all();
        if ($roles && count($roles) && count($roles) != 0) {
            foreach ($roles as $data) {
                foreach ($languages as $language) {
                    JobRoleTranslation::create([
                        'job_role_id' => $data->id,
                        'locale' => $language->code,
                        'name' => $data->name ?? "{$language->code} name",
                    ]);
                }
            }
        }
    }
}
