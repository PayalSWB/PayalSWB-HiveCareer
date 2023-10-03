<?php

namespace Modules\Testimonial\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Testimonial\Entities\Testimonial;

class TestimonialDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $testimonials = [
            [
                'name' => 'Emily Johnson',
                'position' => 'TechFusion Solutions',
                'description' => 'Jobpilot has been a game-changer for my job search. I found my dream job in no time, thanks to their user-friendly interface and extensive job listings. Highly recommended!',
                'image' => fake()->imageUrl,
                'code' => 'en',
                'stars' => 5,
            ],
            [
                'name' => 'David Rodriguez',
                'position' => 'InnovateX Services',
                'description' => "I can't thank Jobpilot enough for their exceptional services. Their personalized job recommendations and application tracking made my job hunt a breeze. I landed an incredible job opportunity that perfectly matched my skills",
                'image' => fake()->imageUrl,
                'code' => 'en',
                'stars' => 5,
            ],
            [
                'name' => 'Sarah Williams',
                'position' => 'ProTech Industries',
                'description' => "Jobpilot truly understands job seekers' needs. Their platform allowed me to connect with top employers and secure multiple interview invitations. I'm now employed in a company I had only dreamed of before.",
                'image' => fake()->imageUrl,
                'code' => 'en',
                'stars' => 5,
            ],
            [
                'name' => 'Michael Thompson',
                'position' => 'GlobalTech Innovations',
                'description' => "Finding the right job seemed daunting until I discovered Jobpilot. The platform's seamless navigation and efficient filters helped me find a job that aligns perfectly with my career goals. Thank you for making my job search fruitful and enjoyable.",
                'image' => fake()->imageUrl,
                'code' => 'bn',
                'stars' => 5,
            ],
            [
                'name' => 'Jennifer Lee',
                'position' => 'TechGenius Solutions',
                'description' => "I've tried several job search platforms, but Jobpilot stands out for its accuracy and relevance. The job matching algorithm is impressive and helped me find a job that perfectly fits my skills and preferences. Thank you, Jobpilot!",
                'image' => fake()->imageUrl,
                'code' => 'bn',
                'stars' => 5,
            ],
            [
                'name' => 'Robert Carter',
                'position' => 'NexTech Innovations',
                'description' => "Jobpilot is a game-changer in the job hunting scene. The platform's user-friendly interface and comprehensive job listings make it incredibly easy to find and apply for jobs. I'm thrilled with the new job opportunity I secured through Jobpilot.",
                'image' => fake()->imageUrl,
                'code' => 'bn',
                'stars' => 5,
            ],
            [
                'name' => 'Laura Martinez',
                'position' => 'SwiftHire Solutions',
                'description' => "As a recent graduate, I was struggling to find a job in my field. Jobpilot not only helped me discover relevant job openings but also provided resources to enhance my job search skills. Thanks to them, I'm now employed at a prestigious company.",
                'image' => fake()->imageUrl,
                'code' => 'ar',
                'stars' => 5,
            ],
            [
                'name' => 'John Davis',
                'position' => 'TechNex Corporation',
                'description' => "Jobpilot made my job search efficient and productive. The platform's advanced filters and regular job alerts simplified the process, and their application tracking system helped me stay organized throughout. I highly recommend Jobpilot to anyone in need of a job.",
                'image' => fake()->imageUrl,
                'code' => 'ar',
                'stars' => 5,
            ],
            [
                'name' => 'Mary Wilson',
                'position' => 'TechFusion Solutions',
                'description' => "I was struggling to find a job that matched my skills and preferences. Jobpilot's personalized job recommendations and application tracking system helped me find the perfect job in no time. I'm now employed at a company I had only dreamed of before.",
                'image' => fake()->imageUrl,
                'code' => 'ar',
                'stars' => 5,
            ],
            [
                'name' => 'James Anderson',
                'position' => 'InnovateX Services',
                'description' => "Jobpilot is the best job search platform I've ever used. Their user-friendly interface and extensive job listings made my job hunt a breeze. I'm now employed at a company I had only dreamed of before. Thank you, Jobpilot!",
                'image' => fake()->imageUrl,
                'code' => 'en',
                'stars' => 5,
            ],
            [
                'name' => 'Patricia Moore',
                'position' => 'ProTech Industries',
                'description' => "I can't thank Jobpilot enough for their exceptional services. Their personalized job recommendations and application tracking made my job hunt a breeze. I landed an incredible job opportunity that perfectly matched my skills.",
                'image' => fake()->imageUrl,
                'code' => 'bn',
                'stars' => 5,
            ],
            [
                'name' => 'John Doe',
                'position' => 'TechFusion Solutions',
                'description' => 'Jobpilot has been a game-changer for my job search. I found my dream job in no time, thanks to their user-friendly interface and extensive job listings. Highly recommended!',
                'image' => fake()->imageUrl,
                'code' => 'ar',
                'stars' => 5,
            ],
            [
                'name' => 'Emily Johnson',
                'position' => 'TechFusion Solutions',
                'description' => 'Jobpilot has been a game-changer for my job search. I found my dream job in no time, thanks to their user-friendly interface and extensive job listings. Highly recommended!',
                'image' => fake()->imageUrl,
                'code' => 'bn',
                'stars' => 5,
            ],
            [
                'name' => 'David Rodriguez',
                'position' => 'InnovateX Services',
                'description' => "I can't thank Jobpilot enough for their exceptional services. Their personalized job recommendations and application tracking made my job hunt a breeze. I landed an incredible job opportunity that perfectly matched my skills",
                'image' => fake()->imageUrl,
                'code' => 'bn',
                'stars' => 5,
            ],
            [
                'name' => 'Sarah Williams',
                'position' => 'ProTech Industries',
                'description' => "Jobpilot truly understands job seekers' needs. Their platform allowed me to connect with top employers and secure multiple interview invitations. I'm now employed in a company I had only dreamed of before.",
                'image' => fake()->imageUrl,
                'code' => 'bn',
                'stars' => 5,
            ],
            [
                'name' => 'Michael Thompson',
                'position' => 'GlobalTech Innovations',
                'description' => "Finding the right job seemed daunting until I discovered Jobpilot. The platform's seamless navigation and efficient filters helped me find a job that aligns perfectly with my career goals. Thank you for making my job search fruitful and enjoyable.",
                'image' => fake()->imageUrl,
                'code' => 'ar',
                'stars' => 5,
            ],
            [
                'name' => 'Jennifer Lee',
                'position' => 'TechGenius Solutions',
                'description' => "I've tried several job search platforms, but Jobpilot stands out for its accuracy and relevance. The job matching algorithm is impressive and helped me find a job that perfectly fits my skills and preferences. Thank you, Jobpilot!",
                'image' => fake()->imageUrl,
                'code' => 'ar',
                'stars' => 5,
            ],
            [
                'name' => 'Robert Carter',
                'position' => 'NexTech Innovations',
                'description' => "Jobpilot is a game-changer in the job hunting scene. The platform's user-friendly interface and comprehensive job listings make it incredibly easy to find and apply for jobs. I'm thrilled with the new job opportunity I secured through Jobpilot.",
                'image' => fake()->imageUrl,
                'code' => 'ar',
                'stars' => 5,
            ],
            [
                'name' => 'Laura Martinez',
                'position' => 'SwiftHire Solutions',
                'description' => "As a recent graduate, I was struggling to find a job in my field. Jobpilot not only helped me discover relevant job openings but also provided resources to enhance my job search skills. Thanks to them, I'm now employed at a prestigious company.",
                'image' => fake()->imageUrl,
                'code' => 'en',
                'stars' => 5,
            ],
            [
                'name' => 'John Davis',
                'position' => 'TechNex Corporation',
                'description' => "Jobpilot made my job search efficient and productive. The platform's advanced filters and regular job alerts simplified the process, and their application tracking system helped me stay organized throughout. I highly recommend Jobpilot to anyone in need of a job.",
                'image' => fake()->imageUrl,
                'code' => 'en',
                'stars' => 5,
            ],
            [
                'name' => 'Mary Wilson',
                'position' => 'TechFusion Solutions',
                'description' => "I was struggling to find a job that matched my skills and preferences. Jobpilot's personalized job recommendations and application tracking system helped me find the perfect job in no time. I'm now employed at a company I had only dreamed of before.",
                'image' => fake()->imageUrl,
                'code' => 'en',
                'stars' => 5,
            ],
        ];

        Testimonial::insert($testimonials);
    }
}
