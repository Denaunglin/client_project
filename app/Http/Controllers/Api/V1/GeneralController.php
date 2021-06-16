<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\ResponseHelper;
use App\Helper\translateHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function checkForUpdate(Request $request)
    {
        $lang = $request->header('language');
        $os = $request->operating_system;
        $app_current_version = ($os == 'ios') ? $request->app_current_version_ios : $request->app_current_version_android;
        $app_generation_number = ($os == 'ios') ? $request->gen_ios : $request->gen_android;

        $app_latest_version = ($os == 'ios') ? config('app.app_latest_version_ios') : config('app.app_latest_version_android');
        $app_latest_generation_number = ($os == 'ios') ? config('app.app_latest_generation_number_ios') : config('app.app_latest_generation_number_android');
        $app_latest_link = ($os == 'ios') ? config('app.apexhotel_appstore_url') : config('app.apexhotel_playstore_url');

        if ($app_generation_number && $app_latest_generation_number) {
            if ($app_latest_generation_number == $app_generation_number) {
                return ResponseHelper::success(
                    [
                        'up_to_date' => 1,
                        'operating_system' => $os,
                        'app_current_version' => $app_current_version,
                        'app_latest_version' => $app_latest_version,
                        'app_latest_link' => $app_latest_link,
                        'highlight_message' => translateHelper::translate('Apex Hotel is temporarily closed due to the Covid-19 Pandemic. Thank you for your understanding during this period !', 'Covid-19 ကပ်ရောဂါကာလ ဖြစ်နေသည့်အတွက် Hotel အား ခေတ္တယာယီ ပိတ်ထားပါသည်။ နားလည်ပေးမှုအတွက် ကျေးဇူးတင်ပါသည်ရှင်။ !', $lang),
                        'hotel_phoneno' => " 09-256328604 ",

                    ],
                    translateHelper::translate('Your Apex Hotel App is up to update.', 'လူကြီးမင်း၏ Apex Hotel အယ်လ်ပလီကေးရှင်းသည် နောက်ဆုံးဗားရှင်းဖြစ်ပါသည်။', $lang)
                );
            } else if ($app_latest_generation_number < $app_generation_number) {
                return ResponseHelper::success(
                    [
                        'up_to_date' => 1,
                        'operating_system' => $os,
                        'app_current_version' => $app_current_version,
                        'app_latest_version' => $app_latest_version,
                        'app_latest_link' => null,
                        'highlight_message' => translateHelper::translate('Apex Hotel is temporarily closed due to the Covid-19 Pandemic. Thank you for your understanding during this period !', 'Covid-19 ကပ်ရောဂါကာလ ဖြစ်နေသည့်အတွက် Hotel အား ခေတ္တယာယီ ပိတ်ထားပါသည်။ နားလည်ပေးမှုအတွက် ကျေးဇူးတင်ပါသည်ရှင်။ !', $lang),
                        'hotel_phoneno' => " 09-256328604 ",

                    ],
                    translateHelper::translate('Please wait, website is under maintenance.', '၀ဘ်ဆိုက် ပြင်ဆင်မှုပြုလုပ်နေပါသဖြင့် ကျေးဇူးပြု၍ စောင့်ဆိုင်းပေးပါခင်ဗျာ။', $lang)
                );
            } else if ($app_latest_generation_number > $app_generation_number) {
                return ResponseHelper::success(
                    [
                        'up_to_date' => 0,
                        'operating_system' => $os,
                        'app_current_version' => $app_current_version,
                        'app_latest_version' => $app_latest_version,
                        'app_latest_link' => $app_latest_link,
                        'highlight_message' => translateHelper::translate('Apex Hotel is temporarily closed due to the Covid-19 Pandemic. Thank you for your understanding during this period !', 'Covid-19 ကပ်ရောဂါကာလ ဖြစ်နေသည့်အတွက် Hotel အား ခေတ္တယာယီ ပိတ်ထားပါသည်။ နားလည်ပေးမှုအတွက် ကျေးဇူးတင်ပါသည်ရှင်။ !', $lang),
                        'hotel_phoneno' => " 09-256328604 ",

                    ],
                    translateHelper::translate('You are using the old version of Apex Hotel . Please update application.', 'လူကြီးမင်းသည် Apex Hotel ဗားရှင်းအဟောင်းကိုအသုံးပြုနေပါသည်။ ကျေးဇူးပြု၍ ဗားရှင်းအသစ်တင်ပေးပါ။', $lang)
                );
            }
        }

        return ResponseHelper::success(
            [
                'up_to_date' => 0,
                'operating_system' => $os,
                'app_current_version' => $app_current_version,
                'app_latest_version' => $app_latest_version,
                'app_latest_link' => $app_latest_link,
                'highlight_message' => translateHelper::translate('Apex Hotel is temporarily closed due to the Covid-19 Pandemic. Thank you for your understanding during this period !', 'Covid-19 ကပ်ရောဂါကာလ ဖြစ်နေသည့်အတွက် Hotel အား ခေတ္တယာယီ ပိတ်ထားပါသည်။ နားလည်ပေးမှုအတွက် ကျေးဇူးတင်ပါသည်ရှင်။ !', $lang),
                'hotel_phoneno' => " 09-256328604 ",

            ],
            translateHelper::translate('You are using the old version of Apex Hotel. Please update application.', 'လူကြီးမင်းသည် Apex Hotel ဗားရှင်းအဟောင်းကိုအသုံးပြုနေပါသည်။ ကျေးဇူးပြု၍ ဗားရှင်းအသစ်တင်ပေးပါ။', $lang)
        );

    }
}
