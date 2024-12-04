<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SonodnamelistSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id' => 1, 'service_id' => 1, 'bnname' => 'নাগরিকত্ব সনদ', 'enname' => 'Citizenship certificate', 'icon' => 'https://www.uniontax.gov.bd/public/assets/icon/1662846491____39554.png', 'template' => '&nbsp; &nbsp; &nbsp; আমি তার ভবিষ্যৎ জীবনের সর্বাঙ্গীন উন্নতি ও মঙ্গল কামনা করি।', 'sonod_fee' => 0, 'created_at' => null, 'updated_at' => '2024-11-15 22:42:43'],
            ['id' => 2, 'service_id' => 2, 'bnname' => 'ট্রেড লাইসেন্স', 'enname' => 'Trade license', 'icon' => 'https://www.uniontax.gov.bd/public/assets/icon/1662995863____49003.png', 'template' => '&nbsp; &nbsp; &nbsp; আমি তার ভবিষ্যৎ জীবনের সর্বাঙ্গীন উন্নতি ও মঙ্গল কামনা করি।', 'sonod_fee' => 0, 'created_at' => null, 'updated_at' => '2024-11-15 22:39:42'],
            ['id' => 3, 'service_id' => 3, 'bnname' => 'ওয়ারিশান সনদ', 'enname' => 'Certificate of Inheritance', 'icon' => 'https://www.uniontax.gov.bd/public/assets/icon/1662995863____49003.png', 'template' => '&nbsp; &nbsp; &nbsp; আমি তার ভবিষ্যৎ জীবনের সর্বাঙ্গীন উন্নতি ও মঙ্গল কামনা করি।', 'sonod_fee' => 0, 'created_at' => null, 'updated_at' => '2024-11-15 22:40:15'],
            ['id' => 4, 'service_id' => 4, 'bnname' => 'উত্তরাধিকারী সনদ', 'enname' => 'Inheritance certificate', 'icon' => 'assets/icon/1733277020____23744.png', 'template' => '&nbsp; &nbsp; &nbsp; আমি তার ভবিষ্যৎ জীবনের সর্বাঙ্গীন উন্নতি ও মঙ্গল কামনা করি।', 'sonod_fee' => 100, 'created_at' => null, 'updated_at' => '2024-12-04 06:50:20'],
            ['id' => 5, 'service_id' => 5, 'bnname' => 'বিবিধ প্রত্যয়নপত্র', 'enname' => 'Miscellaneous certificates', 'icon' => 'https://www.uniontax.gov.bd/public/assets/icon/1662995863____49003.png', 'template' => '&nbsp; &nbsp; &nbsp; আমি তার ভবিষ্যৎ জীবনের সর্বাঙ্গীন উন্নতি ও মঙ্গল কামনা করি।', 'sonod_fee' => 100, 'created_at' => null, 'updated_at' => null],
            ['id' => 6, 'service_id' => 6, 'bnname' => 'চারিত্রিক সনদ', 'enname' => 'Certificate of Character', 'icon' => 'https://www.uniontax.gov.bd/public/assets/icon/1662995863____49003.png', 'template' => '&nbsp; &nbsp; &nbsp; আমি তার ভবিষ্যৎ জীবনের সর্বাঙ্গীন উন্নতি ও মঙ্গল কামনা করি।', 'sonod_fee' => 100, 'created_at' => null, 'updated_at' => null],
            ['id' => 7, 'service_id' => 7, 'bnname' => 'ভূমিহীন সনদ', 'enname' => 'Landless certificate', 'icon' => 'https://www.uniontax.gov.bd/public/assets/icon/1662995863____49003.png', 'template' => '&nbsp; &nbsp; &nbsp; আমি তার ভবিষ্যৎ জীবনের সর্বাঙ্গীন উন্নতি ও মঙ্গল কামনা করি।', 'sonod_fee' => 200, 'created_at' => null, 'updated_at' => '2023-01-05 10:45:26'],
            ['id' => 8, 'service_id' => 8, 'bnname' => 'পারিবারিক সনদ', 'enname' => 'Family certificate', 'icon' => 'https://www.uniontax.gov.bd/public/assets/icon/1662995863____49003.png', 'template' => '&nbsp; &nbsp; &nbsp; আমি তার ভবিষ্যৎ জীবনের সর্বাঙ্গীন উন্নতি ও মঙ্গল কামনা করি।', 'sonod_fee' => 100, 'created_at' => null, 'updated_at' => null],
            ['id' => 9, 'service_id' => 9, 'bnname' => 'অবিবাহিত সনদ', 'enname' => 'Unmarried certificate', 'icon' => 'https://www.uniontax.gov.bd/public/assets/icon/1662995863____49003.png', 'template' => '&nbsp; &nbsp; &nbsp; আমি তার ভবিষ্যৎ জীবনের সর্বাঙ্গীন উন্নতি ও মঙ্গল কামনা করি।', 'sonod_fee' => 100, 'created_at' => null, 'updated_at' => null],
            ['id' => 10, 'service_id' => 10, 'bnname' => 'পুনঃ বিবাহ না হওয়া সনদ', 'enname' => 'Certificate of not remarrying', 'icon' => 'https://www.uniontax.gov.bd/public/assets/icon/1662995863____49003.png', 'template' => '&nbsp; &nbsp; &nbsp; আমি তার ভবিষ্যৎ জীবনের সর্বাঙ্গীন উন্নতি ও মঙ্গল কামনা করি।', 'sonod_fee' => 100, 'created_at' => null, 'updated_at' => null],
            ['id' => 11, 'service_id' => 11, 'bnname' => 'বার্ষিক আয়ের প্রত্যয়ন', 'enname' => 'Certificate of annual income', 'icon' => 'https://www.uniontax.gov.bd/public/assets/icon/1662995863____49003.png', 'template' => '&nbsp; &nbsp; &nbsp; আমি তার ভবিষ্যৎ জীবনের সর্বাঙ্গীন উন্নতি ও মঙ্গল কামনা করি।', 'sonod_fee' => 100, 'created_at' => null, 'updated_at' => null],
            ['id' => 12, 'service_id' => 12, 'bnname' => 'একই নামের প্রত্যয়ন', 'enname' => 'Certification of the same name', 'icon' => 'https://www.uniontax.gov.bd/public/assets/icon/1662995863____49003.png', 'template' => '&nbsp; &nbsp; &nbsp; আমি তার ভবিষ্যৎ জীবনের সর্বাঙ্গীন উন্নতি ও মঙ্গল কামনা করি।', 'sonod_fee' => 100, 'created_at' => null, 'updated_at' => null],
            ['id' => 14, 'service_id' => 13, 'bnname' => 'প্রতিবন্ধী সনদপত্র', 'enname' => 'Disability application', 'icon' => 'https://www.uniontax.gov.bd/public/assets/icon/1662995863____49003.png', 'template' => '&nbsp; &nbsp; &nbsp; আমি তার ভবিষ্যৎ জীবনের সর্বাঙ্গীন উন্নতি ও মঙ্গল কামনা করি।', 'sonod_fee' => 0, 'created_at' => null, 'updated_at' => '2024-11-15 22:40:04'],
            ['id' => 15, 'service_id' => 14, 'bnname' => 'অনাপত্তি সনদপত্র', 'enname' => 'Certificate of No Objection', 'icon' => 'https://www.uniontax.gov.bd/public/assets/icon/1662995863____49003.png', 'template' => '&nbsp; &nbsp; &nbsp; আমি উক্ত প্রতিষ্ঠানটির সার্বিক উন্নতি কমনা করছি।', 'sonod_fee' => 100, 'created_at' => '2022-10-30 09:37:28', 'updated_at' => null],
            ['id' => 28, 'service_id' => 15, 'bnname' => 'আর্থিক অস্বচ্ছলতার সনদপত্র', 'enname' => 'Certificate of Financial Insolvency', 'icon' => 'https://www.uniontax.gov.bd/public/assets/icon/1662995863____49003.png', 'template' => '&nbsp; &nbsp; &nbsp; আমি তার ভবিষ্যৎ জীবনের সর্বাঙ্গীন উন্নতি ও মঙ্গল কামনা করি।', 'sonod_fee' => 100, 'created_at' => null, 'updated_at' => null],
        ];

        foreach ($data as $entry) {
            DB::table('sonodnamelists')->insert($entry);
        }
    }
}
