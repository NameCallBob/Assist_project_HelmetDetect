<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            $user_name = $this->generateTaiwanName();
            $user_phone = $this->generateTaiwanPhoneNumber();
            $user_password = $user_phone; // 统一密码

            DB::table('users')->insert([

                [
                    'name' => $faker->name,
                    'phone' => $faker->unique()->phoneNumber,
                    'email' => $faker->unique()->safeEmail,
                    'account' => $faker->unique()->phoneNumber,
                    'password' => Hash::make('password'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                


            ]);
        }

    }




    private function generateTaiwanPhoneNumber()
    {
        $prefixes = ['0910', '0911', '0912', '0913', '0914', '0915', '0916', '0917', '0918', '0919', '0920', '0921', '0922', '0923', '0924', '0925', '0926', '0927', '0928', '0929'];
        $prefix = $prefixes[array_rand($prefixes)];
        $number = $prefix . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        return $number;
    }

    private function generateTaiwanName()
    {
        $lastNames = ['陳', '林', '黃', '張', '李', '王', '吳', '劉', '蔡', '楊'];
        $firstNames = ['淑芬', '雅婷', '志明', '建銘', '佳玲', '怡君', '家豪', '俊傑', '志豪', '佩芬'];

        $lastName = $lastNames[array_rand($lastNames)];
        $firstName = $firstNames[array_rand($firstNames)];

        return $lastName . $firstName;
    }
}
