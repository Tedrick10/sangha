<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageLocalizationSeeder extends Seeder
{
    public function run(): void
    {
        $packs = [
            'my' => [
                'home' => 'ပင်မ',
                'menu_examinations' => 'စာမေးပွဲများ',
                'menu_about' => 'အကြောင်း',
                'menu_help' => 'အကူအညီ',
                'menu_more' => 'အခြား',
                'main_navigation' => 'မီနူးအဓိက',
                'footer_tagline' => 'ကျောင်းတိုက် စာမေးပွဲများ၊ စာရင်းသွင်းခြင်းနှင့် ရလဒ်များ—ရှင်းလင်းစွာ ပြသထားသည်။',
                'register' => 'စာရင်းသွင်းမည်',
                'home_hero_badge' => 'တရားဝင် စာမေးပွဲ ပလက်ဖောင်း',
                'home_hero_title' => 'ကျောင်းတိုက်နှင့် သံဃာအတွက် ပါဠိ၊ တရားတော်စာမေးပွဲများ',
                'home_hero_subtitle' => 'ကျောင်းတိုက် စာရင်းသွင်းပါ၊ သင်တန်းသားများ မှတ်ပုံတင်ပါ၊ အချိန်ဇယားနှင့် ရလဒ်များကို တစ်နေရာတည်းတွင် ရယူပါ။',
                'home_hero_login' => 'အကောင့်ရှိပြီးသား လား? ဝင်မည်',
                'home_stats_label' => 'တစ်ချက်ကြည့်',
                'home_stat_monasteries_hint' => 'လက်တွဲကျောင်းတိုက် အသင်းဝင်များ',
                'home_stat_sanghas_hint' => 'မှတ်ပုံတင်ထားသော သင်တန်းသား',
                'home_stat_exams_hint' => 'ထုတ်ဝေထားသော စာမေးပွဲအစီအစဉ်များ',
                'home_upcoming_exams' => 'လာမည့် စာမေးပွဲများ',
                'home_upcoming_exams_sub' => 'ဒေတာဘေ့စ်မှ နမူနာ အချိန်ဇယား—ဒမိုပြသရန် အဆင်ပြေသည်။',
                'home_view_schedule' => 'အချိန်ဇယား အပြည့်အစုံ',
                'home_exam_date' => 'ရက်စွဲ',
                'home_exam_venue' => 'နေရာ',
                'home_explore_title' => 'ဝက်ဘ်ဆိုက် လေ့လာရန်',
                'home_explore_sub' => 'မူဝါဒများ၊ အချိန်ဇယားနှင့် အကူအညီ—ဖုန်း၊ တက်ဘလက်နှင့် ကွန်ပျူတာအားလုံးတွင် အသုံးဝင်အောင်။',
                'login' => 'ဝင်မည်',
                'register' => 'စာရင်းသွင်းမည်',
                'admin_panel' => 'အက်မင်ပင်နယ်',
                'quick_links' => 'အမြန်လင့်ခ်များ',
                'pass_sangha_list' => 'အောင်စာရင်း (သံဃာ)',
                'monastery' => 'ကျောင်းတိုက်',
                'sangha_label' => 'သံဃာ',
                'monastery_portal' => 'ကျောင်းတိုက် Portal',
                'dashboard' => 'ဒက်ရှ်ဘုတ်',
                'menu' => 'မီနူး',
                'exams' => 'စာမေးပွဲများ',
                'information' => 'အချက်အလက်',
                'search' => 'ရှာဖွေရန်',
                'filter' => 'စစ်ထုတ်ရန်',
                'save' => 'သိမ်းမည်',
                'cancel' => 'မလုပ်တော့',
                'exit' => 'ထွက်မည်',
                'username' => 'အသုံးပြုသူအမည်',
                'password' => 'စကားဝှက်',
                'confirm_password' => 'စကားဝှက်အတည်ပြု',
                'name' => 'အမည်',
                'description' => 'ဖော်ပြချက်',
                'city' => 'မြို့',
                'region' => 'တိုင်း/ပြည်နယ်',
                'address' => 'လိပ်စာ',
                'phone' => 'ဖုန်း',
                'exam' => 'စာမေးပွဲ',
                'optional' => '(ရွေးချယ်နိုင်)',
                'select_exam' => 'စာမေးပွဲရွေးပါ',
                'select_monastery' => 'ကျောင်းတိုက်ရွေးပါ',
                'custom_fields' => 'စိတ်ကြိုက် Fields',
                'remember_me' => 'မှတ်ထားမည်',
                'register_monastery' => 'ကျောင်းတိုက်စာရင်းသွင်းမည်',
                'register_sangha' => 'သံဃာစာရင်းသွင်းမည်',
                'total' => 'စုစုပေါင်း',
                'pending' => 'စောင့်ဆိုင်းနေ',
                'approved' => 'အတည်ပြုပြီး',
                'rejected' => 'ငြင်းပယ်ပြီး',
                'request' => 'တောင်းဆိုချက်',
                'results' => 'ရလဒ်များ',
                'main' => 'ပင်မ',
                'pass_list' => 'အောင်စာရင်း',
                'fail_list' => 'မအောင်စာရင်း',
                'send_message' => 'စာပို့မည်',
                'additional_message' => 'ထပ်ဆောင်းစာ',
            ],
            'th' => [
                'home' => 'หน้าหลัก', 'menu_examinations' => 'การสอบ', 'menu_more' => 'เพิ่มเติม',
                'login' => 'เข้าสู่ระบบ', 'register' => 'ลงทะเบียน', 'admin_panel' => 'แผงผู้ดูแล',
                'quick_links' => 'ลิงก์ด่วน', 'pass_sangha_list' => 'รายชื่อสอบผ่าน',
                'monastery' => 'วัด', 'sangha_label' => 'สงฆ์', 'monastery_portal' => 'พอร์ทัลวัด',
                'dashboard' => 'แดชบอร์ด', 'save' => 'บันทึก', 'cancel' => 'ยกเลิก',
            ],
            'si' => [
                'home' => 'මුල් පිටුව', 'menu_examinations' => 'විභාග', 'menu_more' => 'තවත්',
                'login' => 'පිවිසුම', 'register' => 'ලියාපදිංචි වීම', 'admin_panel' => 'පරිපාලක පුවරුව',
                'quick_links' => 'ඉක්මන් සබැඳි', 'pass_sangha_list' => 'සමත් සංඝ ලැයිස්තුව',
                'monastery' => 'විහාරය', 'sangha_label' => 'සංඝ', 'dashboard' => 'පාලක පුවරුව',
            ],
            'pi' => [
                'home' => 'Geha', 'menu_examinations' => 'Parikkhā', 'menu_more' => 'Aparaṃ',
                'login' => 'Pavisati', 'register' => 'Lekhāpeti', 'admin_panel' => 'Adhipati Maṇḍala',
                'quick_links' => 'Sīgha-sambandhā', 'pass_sangha_list' => 'Jina-saṅgha nāmāvalī',
                'monastery' => 'Vihāra', 'sangha_label' => 'Saṅgha',
            ],
            'zh' => [
                'home' => '首页', 'menu_examinations' => '考试', 'menu_more' => '更多',
                'login' => '登录', 'register' => '注册', 'admin_panel' => '管理后台',
                'quick_links' => '快捷链接', 'pass_sangha_list' => '通过僧伽名单',
                'monastery' => '寺院', 'sangha_label' => '僧伽', 'dashboard' => '仪表盘',
            ],
            'km' => [
                'home' => 'ទំព័រដើម', 'menu_examinations' => 'ការប្រឡង', 'menu_more' => 'បន្ថែម',
                'login' => 'ចូល', 'register' => 'ចុះឈ្មោះ', 'admin_panel' => 'ផ្ទាំងគ្រប់គ្រង',
                'quick_links' => 'តំណរហ័ស', 'pass_sangha_list' => 'បញ្ជីសង្ឃជាប់',
                'monastery' => 'វត្ត', 'sangha_label' => 'សង្ឃ',
            ],
            'lo' => [
                'home' => 'ໜ້າຫຼັກ', 'menu_examinations' => 'ການສອບເສັງ', 'menu_more' => 'ເພີ່ມເຕີມ',
                'login' => 'ເຂົ້າລະບົບ', 'register' => 'ລົງທະບຽນ', 'admin_panel' => 'ແຜງຜູ້ຄຸ້ມຄອງ',
                'quick_links' => 'ລິ້ງດ່ວນ', 'pass_sangha_list' => 'ລາຍຊື່ສົງຜ່ານ',
                'monastery' => 'ວັດ', 'sangha_label' => 'ສົງ',
            ],
        ];

        foreach ($packs as $code => $translations) {
            $language = Language::query()->where('code', $code)->first();
            if (! $language) {
                continue;
            }
            foreach ($translations as $key => $value) {
                $language->translations()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }
    }
}
