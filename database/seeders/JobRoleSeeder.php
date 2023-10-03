<?php

namespace Database\Seeders;

use App\Models\JobRole;
use Illuminate\Database\Seeder;
use Modules\Language\Entities\Language;

class JobRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! config('zakirsoft.testing_mode')) {
            $job_roles = [
                'Team Leader', 'Manager', 'Assistant Manager', 'Executive', 'Director', 'Administrator',
            ];
        } else {
            $job_roles = [
                'Team Leader', 'Manager',
            ];
        }

        // foreach ($job_roles as $data) {
        //     JobRole::create([
        //         'name' => $data
        //     ]);
        // }

        $languages = Language::all();

        foreach ($job_roles as $data) {
            $translation = new JobRole();
            $translation->save();

            foreach ($languages as $language) {
                $translation->translateOrNew($language->code)->name = $data;
            }

            $translation->save();
        }
    }
}
