<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TradeLicenseKhatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['id' => 1, 'khat_id' => '101', 'name' => 'গুদাম (লিমিটেড কোম্পানী ব্যতীত)', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 2, 'khat_id' => '101101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 101, 'type' => 'sub'],
            ['id' => 3, 'khat_id' => '101102', 'name' => 'মূলধন ১ লক্ষ টাকা হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 101, 'type' => 'sub'],
            ['id' => 4, 'khat_id' => '101103', 'name' => 'মূলধন ৫ লক্ষ টাকা হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 101, 'type' => 'sub'],
            ['id' => 5, 'khat_id' => '101104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 101, 'type' => 'sub'],
            ['id' => 6, 'khat_id' => '102', 'name' => 'হিমাগার (লিমিটেড কোম্পানী ব্যতীত)', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 7, 'khat_id' => '102101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 102, 'type' => 'sub'],
            ['id' => 8, 'khat_id' => '102102', 'name' => 'মূলধন ১ লক্ষ টাকা হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 102, 'type' => 'sub'],
            ['id' => 9, 'khat_id' => '102103', 'name' => 'মূলধন ৫ লক্ষ টাকা হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 102, 'type' => 'sub'],
            ['id' => 10, 'khat_id' => '102104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 102, 'type' => 'sub'],
            ['id' => 11, 'khat_id' => '103', 'name' => 'লবণ মিল', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 12, 'khat_id' => '103101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 103, 'type' => 'sub'],
            ['id' => 13, 'khat_id' => '103102', 'name' => 'মূলধন ১ লক্ষ টাকা হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 103, 'type' => 'sub'],
            ['id' => 14, 'khat_id' => '103103', 'name' => 'মূলধন ৫ লক্ষ টাকা হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 103, 'type' => 'sub'],
            ['id' => 15, 'khat_id' => '103104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 103, 'type' => 'sub'],
            ['id' => 16, 'khat_id' => '104', 'name' => 'জাহাজঘাটা (লিমিটেড কোম্পানী ব্যতীত)', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 17, 'khat_id' => '104101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 104, 'type' => 'sub'],
            ['id' => 18, 'khat_id' => '104102', 'name' => 'মূলধন ১ লক্ষ টাকা হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 104, 'type' => 'sub'],
            ['id' => 19, 'khat_id' => '104103', 'name' => 'মূলধন ৫ লক্ষ টাকা হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 104, 'type' => 'sub'],
            ['id' => 20, 'khat_id' => '104104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 104, 'type' => 'sub'],
            ['id' => 21, 'khat_id' => '105', 'name' => 'ধান চাল বা চালের কারখানা', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 22, 'khat_id' => '105101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 105, 'type' => 'sub'],
            ['id' => 23, 'khat_id' => '105102', 'name' => 'মূলধন ১ লক্ষ হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 105, 'type' => 'sub'],
            ['id' => 24, 'khat_id' => '105103', 'name' => 'মূলধন ৫ লক্ষ হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 105, 'type' => 'sub'],
            ['id' => 25, 'khat_id' => '105104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 105, 'type' => 'sub'],
            ['id' => 26, 'khat_id' => '106', 'name' => 'চিনির কারখানা', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 27, 'khat_id' => '106101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 106, 'type' => 'sub'],
            ['id' => 28, 'khat_id' => '106102', 'name' => 'মূলধন ১ লক্ষ হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 106, 'type' => 'sub'],
            ['id' => 29, 'khat_id' => '106103', 'name' => 'মূলধন ৫ লক্ষ হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 106, 'type' => 'sub'],
            ['id' => 30, 'khat_id' => '106104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 106, 'type' => 'sub'],
            ['id' => 31, 'khat_id' => '107', 'name' => 'লবণের কারখানা', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 32, 'khat_id' => '107101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 107, 'type' => 'sub'],
            ['id' => 33, 'khat_id' => '107102', 'name' => 'মূলধন ১ লক্ষ হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 107, 'type' => 'sub'],
            ['id' => 34, 'khat_id' => '107103', 'name' => 'মূলধন ৫ লক্ষ হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 107, 'type' => 'sub'],
            ['id' => 35, 'khat_id' => '107104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 107, 'type' => 'sub'],
            ['id' => 36, 'khat_id' => '108', 'name' => 'তৈল পরিশোধন কারখানা', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 37, 'khat_id' => '108101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 108, 'type' => 'sub'],
            ['id' => 38, 'khat_id' => '108102', 'name' => 'মূলধন ১ লক্ষ হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 108, 'type' => 'sub'],
            ['id' => 39, 'khat_id' => '108103', 'name' => 'মূলধন ৫ লক্ষ হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 108, 'type' => 'sub'],
            ['id' => 40, 'khat_id' => '108104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 108, 'type' => 'sub'],
            ['id' => 41, 'khat_id' => '109', 'name' => 'বস্ত্রশিল্প কারখানা', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 42, 'khat_id' => '109101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 109, 'type' => 'sub'],
            ['id' => 43, 'khat_id' => '109102', 'name' => 'মূলধন ১ লক্ষ হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 109, 'type' => 'sub'],
            ['id' => 44, 'khat_id' => '109103', 'name' => 'মূলধন ৫ লক্ষ হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 109, 'type' => 'sub'],
            ['id' => 45, 'khat_id' => '109104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 109, 'type' => 'sub'],
            ['id' => 46, 'khat_id' => '110', 'name' => 'চামড়া প্রক্রিয়াজাতকরণ কারখানা', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 47, 'khat_id' => '110101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 110, 'type' => 'sub'],
            ['id' => 48, 'khat_id' => '110102', 'name' => 'মূলধন ১ লক্ষ হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 110, 'type' => 'sub'],
            ['id' => 49, 'khat_id' => '110103', 'name' => 'মূলধন ৫ লক্ষ হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 110, 'type' => 'sub'],
            ['id' => 50, 'khat_id' => '110104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 110, 'type' => 'sub'],
            ['id' => 51, 'khat_id' => '111', 'name' => 'কাগজ ও মুদ্রণশিল্প কারখানা', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 52, 'khat_id' => '111101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 111, 'type' => 'sub'],
            ['id' => 53, 'khat_id' => '111102', 'name' => 'মূলধন ১ লক্ষ হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 111, 'type' => 'sub'],
            ['id' => 54, 'khat_id' => '111103', 'name' => 'মূলধন ৫ লক্ষ হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 111, 'type' => 'sub'],
            ['id' => 55, 'khat_id' => '111104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 111, 'type' => 'sub'],
            ['id' => 56, 'khat_id' => '112', 'name' => 'অর্থনীতি ও বাণিজ্যিক অফিস', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 57, 'khat_id' => '112101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 112, 'type' => 'sub'],
            ['id' => 58, 'khat_id' => '112102', 'name' => 'মূলধন ১ লক্ষ হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 112, 'type' => 'sub'],
            ['id' => 59, 'khat_id' => '112103', 'name' => 'মূলধন ৫ লক্ষ হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 112, 'type' => 'sub'],
            ['id' => 60, 'khat_id' => '112104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 112, 'type' => 'sub'],
            ['id' => 61, 'khat_id' => '113', 'name' => 'প্রযুক্তি ও সফটওয়্যার উন্নয়ন প্রতিষ্ঠান', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 62, 'khat_id' => '113101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 113, 'type' => 'sub'],
            ['id' => 63, 'khat_id' => '113102', 'name' => 'মূলধন ১ লক্ষ হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 113, 'type' => 'sub'],
            ['id' => 64, 'khat_id' => '113103', 'name' => 'মূলধন ৫ লক্ষ হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 113, 'type' => 'sub'],
            ['id' => 65, 'khat_id' => '113104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 113, 'type' => 'sub'],
            ['id' => 66, 'khat_id' => '114', 'name' => 'খাদ্য প্রক্রিয়াজাতকরণ কারখানা', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 67, 'khat_id' => '114101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 114, 'type' => 'sub'],
            ['id' => 68, 'khat_id' => '114102', 'name' => 'মূলধন ১ লক্ষ হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 114, 'type' => 'sub'],
            ['id' => 69, 'khat_id' => '114103', 'name' => 'মূলধন ৫ লক্ষ হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 114, 'type' => 'sub'],
            ['id' => 70, 'khat_id' => '114104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 114, 'type' => 'sub'],
            ['id' => 71, 'khat_id' => '115', 'name' => 'ঔষধ উৎপাদন প্রতিষ্ঠান', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 72, 'khat_id' => '115101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 115, 'type' => 'sub'],
            ['id' => 73, 'khat_id' => '115102', 'name' => 'মূলধন ১ লক্ষ হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 115, 'type' => 'sub'],
            ['id' => 74, 'khat_id' => '115103', 'name' => 'মূলধন ৫ লক্ষ হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 115, 'type' => 'sub'],
            ['id' => 75, 'khat_id' => '115104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 115, 'type' => 'sub'],
            ['id' => 76, 'khat_id' => '116', 'name' => 'শিল্পকলা এবং সাংস্কৃতিক কেন্দ্র', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 77, 'khat_id' => '116101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 116, 'type' => 'sub'],
            ['id' => 78, 'khat_id' => '116102', 'name' => 'মূলধন ১ লক্ষ হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 116, 'type' => 'sub'],
            ['id' => 79, 'khat_id' => '116103', 'name' => 'মূলধন ৫ লক্ষ হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 116, 'type' => 'sub'],
            ['id' => 80, 'khat_id' => '116104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 116, 'type' => 'sub'],
            ['id' => 81, 'khat_id' => '117', 'name' => 'অটোমোবাইল নির্মাণ ও বিপণন প্রতিষ্ঠান', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 82, 'khat_id' => '117101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 117, 'type' => 'sub'],
            ['id' => 83, 'khat_id' => '117102', 'name' => 'মূলধন ১ লক্ষ হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 117, 'type' => 'sub'],
            ['id' => 84, 'khat_id' => '117103', 'name' => 'মূলধন ৫ লক্ষ হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 117, 'type' => 'sub'],
            ['id' => 85, 'khat_id' => '117104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 117, 'type' => 'sub'],
            ['id' => 86, 'khat_id' => '118', 'name' => 'কম্পিউটার হার্ডওয়্যার উৎপাদন প্রতিষ্ঠান', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 87, 'khat_id' => '118101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 118, 'type' => 'sub'],
            ['id' => 88, 'khat_id' => '118102', 'name' => 'মূলধন ১ লক্ষ হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 118, 'type' => 'sub'],
            ['id' => 89, 'khat_id' => '118103', 'name' => 'মূলধন ৫ লক্ষ হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 118, 'type' => 'sub'],
            ['id' => 90, 'khat_id' => '118104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 118, 'type' => 'sub'],
            ['id' => 91, 'khat_id' => '119', 'name' => 'স্থাপত্য ও নির্মাণ প্রতিষ্ঠান', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 92, 'khat_id' => '119101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 119, 'type' => 'sub'],
            ['id' => 93, 'khat_id' => '119102', 'name' => 'মূলধন ১ লক্ষ হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 119, 'type' => 'sub'],
            ['id' => 94, 'khat_id' => '119103', 'name' => 'মূলধন ৫ লক্ষ হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 119, 'type' => 'sub'],
            ['id' => 95, 'khat_id' => '119104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 119, 'type' => 'sub'],
            ['id' => 96, 'khat_id' => '120', 'name' => 'শিক্ষা ও প্রশিক্ষণ প্রতিষ্ঠান', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 97, 'khat_id' => '120101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 120, 'type' => 'sub'],
            ['id' => 98, 'khat_id' => '120102', 'name' => 'মূলধন ১ লক্ষ হইতে ৫ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 120, 'type' => 'sub'],
            ['id' => 99, 'khat_id' => '120103', 'name' => 'মূলধন ৫ লক্ষ হইতে ১০ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 120, 'type' => 'sub'],
            ['id' => 100, 'khat_id' => '120104', 'name' => 'মূলধন ১০ লক্ষ টাকার ঊর্ধ্বে', 'main_khat_id' => 120, 'type' => 'sub'],
            ['id' => 101, 'khat_id' => '121', 'name' => 'পরিবহন ও লজিস্টিক সেবা', 'main_khat_id' => 0, 'type' => 'main'],
            ['id' => 102, 'khat_id' => '121101', 'name' => 'মূলধন ১ লক্ষ টাকা পর্যন্ত', 'main_khat_id' => 121, 'type' => 'sub'],

     
        ];

        foreach ($data as &$entry) {
            $entry['created_at'] = Carbon::now();
            $entry['updated_at'] = Carbon::now();
        }

        DB::table('trade_license_khats')->insert($data);
    }
}
