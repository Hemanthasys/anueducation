<?php

namespace Database\Seeders;

use App\Models\FundingCategory;
use App\Models\FundingSource;
use Illuminate\Database\Seeder;

class FundingSourceSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'code'     => 'A',
                'label_si' => 'මධ්‍යම රජයේ හා පළාත් සභා ප්‍රතිපාදන',
                'label_en' => 'Central Government & Provincial Council Funds',
                'sources'  => [
                    ['code' => 'S1', 'label_si' => 'ඒකාබද්ධ අරමුදල්/පළාත් සභා අරමුදල්', 'label_en' => 'Consolidated Fund / Provincial Council Fund'],
                    ['code' => 'S2', 'label_si' => 'බහු පාර්ශවීය හා ද්වි-පාර්ශවීය සහයෝගීතා ගිවිසුම් යටතේ ක්‍රියාත්මකවන වැඩසටහන්/ව්‍යාපෘතිවලින් ලැබෙන අරමුදල්', 'label_en' => 'Funds from Multilateral & Bilateral Cooperation Agreement Programmes/Projects'],
                    ['code' => 'S3', 'label_si' => 'රාජ්‍ය අංශයේ වෙනත් ආයතනවලින් ලැබෙන, මහා භාණ්ඩාගාරය/පළාත් භාණ්ඩගාරය අනුමත ආධාර', 'label_en' => 'Grants from Other Public Sector Institutions Approved by Treasury / Provincial Treasury'],
                    ['code' => 'S4', 'label_si' => 'මධ්‍යම රජයේ සහ පළාත් සභා ප්‍රතිපාදන යටතේ පාසල් පාදක ඉගෙනුම් ප්‍රවර්ධන ප්‍යාදානයන්, ගුණාත්මක යෙදවුම් සදහා හා උසස් මට්ටමේ ඉගෙනුම් ක්‍රියාවලීන් සදහා ලැබෙන අරමුදල්', 'label_en' => 'School-Based Learning Promotion Grants Under Central Government & Provincial Council Provisions for Quality Inputs & Advanced Learning Processes'],
                ],
            ],
            [
                'code'     => 'B',
                'label_si' => 'බාහිර අරමුදල්',
                'label_en' => 'External Funds',
                'sources'  => [
                    ['code' => 'S5', 'label_si' => 'රජය විසින් අනුමත හා ලියාපදිංචි රාජ්‍ය නොවන සංවිධානවලින් ලැබෙන ආධාර', 'label_en' => 'Grants from Government-Approved & Registered Non-Governmental Organisations'],
                    ['code' => 'S6', 'label_si' => 'දෙමව්පියන්ගේ/ සුබ පතන්නන්ගේ/ ආදි ශිෂ්‍ය සංගමයේ පරිත්‍යාග', 'label_en' => 'Donations from Parents / Well-Wishers / Old Students\' Association'],
                ],
            ],
            [
                'code'     => 'C',
                'label_si' => 'වෙනත් ප්‍රභවයන්ගෙන් ලැබෙන අරමුදල්',
                'label_en' => 'Funds from Other Sources',
                'sources'  => [
                    ['code' => 'S7',  'label_si' => 'පාසල් ඉඩම්, ගොඩනැගිලි හා වෙනත් ප්‍රභවයන්ගෙන් ලැබෙන ගාස්තු', 'label_en' => 'Fees from School Land, Buildings & Other Sources'],
                    ['code' => 'S8',  'label_si' => 'පාසල් සංවර්ධන සමිතියේ සාමාජිකයන්ගෙන් ලැබෙන සාමාජික අරමුදල්', 'label_en' => 'Membership Funds from School Development Society Members'],
                    ['code' => 'S9',  'label_si' => 'පාසලේ විවිධ ක්‍රියාකාරකම් හා ව්‍යාපෘති මගින් උපයා ගැනීම්', 'label_en' => 'Earnings from Various School Activities & Projects'],
                    ['code' => 'S10', 'label_si' => 'පාසල් සංවර්ධන සමිතිය විසින් තීරණය කරනු ලබන වෙනත් අරමුදල්', 'label_en' => 'Other Funds Determined by the School Development Society'],
                ],
            ],
        ];

        foreach ($data as $categoryData) {
            $sources = $categoryData['sources'];
            unset($categoryData['sources']);

            $category = FundingCategory::updateOrCreate(
                ['code' => $categoryData['code']],
                $categoryData
            );

            foreach ($sources as $source) {
                FundingSource::updateOrCreate(
                    ['code' => $source['code']],
                    array_merge($source, ['funding_category_id' => $category->id, 'is_active' => true])
                );
            }
        }
    }
}
