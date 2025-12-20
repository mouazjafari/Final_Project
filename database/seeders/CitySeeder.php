<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = $this->getCitiesData();

        $citiesToInsert = [];

        foreach ($cities as $cityData) {
            $citiesToInsert[] = [
                'name' => json_encode($cityData, JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($citiesToInsert, 100) as $chunk) {
            DB::table('cities')->insert($chunk);
        }
    }

    public function getCitiesData()
    {
        return [
            // Saudi Arabia - السعودية
            ['en' => 'Riyadh', 'ar' => 'الرياض'],
            ['en' => 'Jeddah', 'ar' => 'جدة'],
            ['en' => 'Mecca', 'ar' => 'مكة المكرمة'],
            ['en' => 'Medina', 'ar' => 'المدينة المنورة'],
            ['en' => 'Dammam', 'ar' => 'الدمام'],
            ['en' => 'Khobar', 'ar' => 'الخبر'],
            ['en' => 'Taif', 'ar' => 'الطائف'],
            ['en' => 'Tabuk', 'ar' => 'تبوك'],
            ['en' => 'Buraidah', 'ar' => 'بريدة'],
            ['en' => 'Hail', 'ar' => 'حائل'],
            ['en' => 'Abha', 'ar' => 'أبها'],
            ['en' => 'Jazan', 'ar' => 'جازان'],
            ['en' => 'Najran', 'ar' => 'نجران'],

            // Egypt - مصر
            ['en' => 'Cairo', 'ar' => 'القاهرة'],
            ['en' => 'Alexandria', 'ar' => 'الإسكندرية'],
            ['en' => 'Giza', 'ar' => 'الجيزة'],
            ['en' => 'Shubra El-Kheima', 'ar' => 'شبرا الخيمة'],
            ['en' => 'Port Said', 'ar' => 'بورسعيد'],
            ['en' => 'Suez', 'ar' => 'السويس'],
            ['en' => 'Luxor', 'ar' => 'الأقصر'],
            ['en' => 'Aswan', 'ar' => 'أسوان'],
            ['en' => 'Mansoura', 'ar' => 'المنصورة'],
            ['en' => 'Tanta', 'ar' => 'طنطا'],

            // UAE - الإمارات
            ['en' => 'Dubai', 'ar' => 'دبي'],
            ['en' => 'Abu Dhabi', 'ar' => 'أبو ظبي'],
            ['en' => 'Sharjah', 'ar' => 'الشارقة'],
            ['en' => 'Ajman', 'ar' => 'عجمان'],
            ['en' => 'Al Ain', 'ar' => 'العين'],
            ['en' => 'Ras Al Khaimah', 'ar' => 'رأس الخيمة'],
            ['en' => 'Fujairah', 'ar' => 'الفجيرة'],

            // Jordan - الأردن
            ['en' => 'Amman', 'ar' => 'عمان'],
            ['en' => 'Zarqa', 'ar' => 'الزرقاء'],
            ['en' => 'Irbid', 'ar' => 'إربد'],
            ['en' => 'Aqaba', 'ar' => 'العقبة'],
            ['en' => 'Karak', 'ar' => 'الكرك'],

            // Lebanon - لبنان
            ['en' => 'Beirut', 'ar' => 'بيروت'],
            ['en' => 'Tripoli', 'ar' => 'طرابلس'],
            ['en' => 'Sidon', 'ar' => 'صيدا'],
            ['en' => 'Tyre', 'ar' => 'صور'],
            ['en' => 'Byblos', 'ar' => 'جبيل'],

            // Iraq - العراق
            ['en' => 'Baghdad', 'ar' => 'بغداد'],
            ['en' => 'Basra', 'ar' => 'البصرة'],
            ['en' => 'Mosul', 'ar' => 'الموصل'],
            ['en' => 'Erbil', 'ar' => 'أربيل'],
            ['en' => 'Sulaymaniyah', 'ar' => 'السليمانية'],
            ['en' => 'Najaf', 'ar' => 'النجف'],
            ['en' => 'Karbala', 'ar' => 'كربلاء'],

            // Kuwait - الكويت
            ['en' => 'Kuwait City', 'ar' => 'الكويت'],
            ['en' => 'Hawalli', 'ar' => 'حولي'],
            ['en' => 'Farwaniya', 'ar' => 'الفروانية'],
            ['en' => 'Ahmadi', 'ar' => 'الأحمدي'],

            // Qatar - قطر
            ['en' => 'Doha', 'ar' => 'الدوحة'],
            ['en' => 'Al Rayyan', 'ar' => 'الريان'],
            ['en' => 'Al Wakrah', 'ar' => 'الوكرة'],
            ['en' => 'Al Khor', 'ar' => 'الشمال'],

            // Bahrain - البحرين
            ['en' => 'Manama', 'ar' => 'المنامة'],
            ['en' => 'Muharraq', 'ar' => 'المحرق'],
            ['en' => 'Riffa', 'ar' => 'الرفاع'],
            ['en' => 'Madinat Hamad', 'ar' => 'مدينة حمد'],

            // Oman - عمان
            ['en' => 'Muscat', 'ar' => 'مسقط'],
            ['en' => 'Salalah', 'ar' => 'صلالة'],
            ['en' => 'Sohar', 'ar' => 'صحار'],
            ['en' => 'Nizwa', 'ar' => 'نزوى'],

            // Yemen - اليمن
            ['en' => 'Sana\'a', 'ar' => 'صنعاء'],
            ['en' => 'Aden', 'ar' => 'عدن'],
            ['en' => 'Taiz', 'ar' => 'تعز'],
            ['en' => 'Hodeidah', 'ar' => 'الحديدة'],
            ['en' => 'Ibb', 'ar' => 'إب'],

            // Syria - سوريا
            ['en' => 'Damascus', 'ar' => 'دمشق'],
            ['en' => 'Aleppo', 'ar' => 'حلب'],
            ['en' => 'Homs', 'ar' => 'حمص'],
            ['en' => 'Latakia', 'ar' => 'اللاذقية'],
            ['en' => 'Hama', 'ar' => 'حماة'],

            // Palestine - فلسطين
            ['en' => 'Jerusalem', 'ar' => 'القدس'],
            ['en' => 'Gaza', 'ar' => 'غزة'],
            ['en' => 'Ramallah', 'ar' => 'رام الله'],
            ['en' => 'Hebron', 'ar' => 'الخليل'],
            ['en' => 'Nablus', 'ar' => 'نابلس'],

            // Morocco - المغرب
            ['en' => 'Casablanca', 'ar' => 'الدار البيضاء'],
            ['en' => 'Rabat', 'ar' => 'الرباط'],
            ['en' => 'Fes', 'ar' => 'فاس'],
            ['en' => 'Marrakech', 'ar' => 'مراكش'],
            ['en' => 'Tangier', 'ar' => 'طنجة'],

            // Algeria - الجزائر
            ['en' => 'Algiers', 'ar' => 'الجزائر'],
            ['en' => 'Oran', 'ar' => 'وهران'],
            ['en' => 'Constantine', 'ar' => 'قسنطينة'],
            ['en' => 'Annaba', 'ar' => 'عنابة'],
            ['en' => 'Batna', 'ar' => 'باتنة'],

            // Tunisia - تونس
            ['en' => 'Tunis', 'ar' => 'تونس'],
            ['en' => 'Sfax', 'ar' => 'صفاقس'],
            ['en' => 'Sousse', 'ar' => 'سوسة'],
            ['en' => 'Ariana', 'ar' => 'أريانة'],
            ['en' => 'Bizerte', 'ar' => 'بنزرت'],

            // Libya - ليبيا
            ['en' => 'Tripoli', 'ar' => 'طرابلس'],
            ['en' => 'Benghazi', 'ar' => 'بنغازي'],
            ['en' => 'Misrata', 'ar' => 'مصراتة'],
            ['en' => 'Sabha', 'ar' => 'سبها'],
            ['en' => 'Tobruk', 'ar' => 'طبرق'],

            // Sudan - السودان
            ['en' => 'Khartoum', 'ar' => 'الخرطوم'],
            ['en' => 'Omdurman', 'ar' => 'أم درمان'],
            ['en' => 'Port Sudan', 'ar' => 'بورتسودان'],
            ['en' => 'Wad Madani', 'ar' => 'ود مدني'],
            ['en' => 'Nyala', 'ar' => 'نيالا'],

            // Somalia - الصومال
            ['en' => 'Mogadishu', 'ar' => 'مقديشو'],
            ['en' => 'Hargeisa', 'ar' => 'هرجيسا'],
            ['en' => 'Bosaso', 'ar' => 'بوساسو'],
            ['en' => 'Kismayo', 'ar' => 'كيسمايو'],

            // Mauritania - موريتانيا
            ['en' => 'Nouakchott', 'ar' => 'نواكشوط'],
            ['en' => 'Nouadhibou', 'ar' => 'نواذيبو'],
            ['en' => 'Kiffa', 'ar' => 'كيفة'],
            ['en' => 'Zouerate', 'ar' => 'زويرات'],

            // Djibouti - جيبوتي
            ['en' => 'Djibouti', 'ar' => 'جيبوتي'],
            ['en' => 'Ali Sabieh', 'ar' => 'علي صبيح'],
            ['en' => 'Tadjoura', 'ar' => 'تاجورة'],

            // Comoros - جزر القمر
            ['en' => 'Moroni', 'ar' => 'موروني'],
            ['en' => 'Mutsamudu', 'ar' => 'متسامودو'],
            ['en' => 'Fomboni', 'ar' => 'فومبوني'],
        ];
    }
}
