<?php

namespace Modules\Currency\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Currency\Entities\Currency;

class CurrencyDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $currencies = [
            [
                'name' => 'United States Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'symbol_position' => 'left',
            ],
            [
                'name' => 'Indian Rupee',
                'code' => 'INR',
                'symbol' => '₹',
                'symbol_position' => 'left',
            ],
            [
                'name' => 'Australian Dollar',
                'code' => 'AUD',
                'symbol' => '$',
                'symbol_position' => 'left',
            ],
            [
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'symbol_position' => 'left',
            ],
            [
                'name' => 'Bangladeshi Taka',
                'code' => 'BDT',
                'symbol' => '৳',
                'symbol_position' => 'left',
            ],
            [
                'name' => 'Indonesian Rupiah',
                'code' => 'IDR',
                'symbol' => 'Rp',
                'symbol_position' => 'left',
            ],
            [
                'name' => 'Pakistani Rupee',
                'code' => 'PKR',
                'symbol' => '₨',
                'symbol_position' => 'left',
            ],
            [
                'name' => 'Nigerian Naira',
                'code' => 'NGN',
                'symbol' => '₦',
                'symbol_position' => 'left',
            ],
            [
                'name' => 'Egyptian Pound',
                'code' => 'EGP',
                'symbol' => '£',
                'symbol_position' => 'left',
            ],
            [
                'name' => 'Turkish Lira',
                'code' => 'TRY',
                'symbol' => '₺',
                'symbol_position' => 'left',
            ],
            [
                'name' => 'Philippine Peso',
                'code' => 'PHP',
                'symbol' => '₱',
                'symbol_position' => 'left',
            ],
            [
                'name' => 'Kenyan Shilling',
                'code' => 'KES',
                'symbol' => 'KSh',
                'symbol_position' => 'left',
            ],
            [
                'name' => 'Nepalese Rupee',
                'code' => 'NPR',
                'symbol' => '₨',
                'symbol_position' => 'left',
            ],
            [
                'name' => 'British Pound Sterling',
                'code' => 'GBP',
                'symbol' => '£',
                'symbol_position' => 'left',
            ],
            [
                'name' => 'Vietnamese Dong',
                'code' => 'VND',
                'symbol' => '₫',
                'symbol_position' => 'left',
            ],
            [
                'name' => 'Brazilian Real',
                'code' => 'BRL',
                'symbol' => 'R$',
                'symbol_position' => 'left',
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
