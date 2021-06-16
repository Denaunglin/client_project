<?php

namespace App\Http\Middleware;

use App\Helper\ResponseHelper;
use App\Helper\translateHelper;
use Closure;

class VersionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $lang = $request->header('language');
        $os = $request->header('operating-system');
        $app_generation_number = ($os == 'ios') ? $request->header('gen-ios') : $request->header('gen-android');
        $app_latest_generation_number = ($os == 'ios') ? config('app.app_latest_generation_number_ios') : config('app.app_latest_generation_number_android');
        $app_miniumn_generation_number = ($os == 'ios') ? config('app.app_minimum_generation_number_ios') : config('app.app_minimum_generation_number_android');

        if ($app_generation_number && $app_latest_generation_number) {
            // if ($app_latest_generation_number == $app_generation_number) {
            //     return $next($request);
            // } else if ($app_latest_generation_number < $app_generation_number) {
            //     return failedMessage(translate('Please wait, website is under maintenance.', '၀ဘ်ဆိုက် ပြင်ဆင်မှုပြုလုပ်နေပါသဖြင့် ကျေးဇူးပြု၍ စောင့်ဆိုင်းပေးပါခင်ဗျာ။', $lang));
            // } else if ($app_latest_generation_number > $app_generation_number) {
            //     return failedMessage(translate('You are using the old version of Starfish. Please update application.', 'လူကြီးမင်းသည် Starfish ဗားရှင်းအဟောင်းကိုအသုံးပြုနေပါသည်။ ကျေးဇူးပြု၍ ဗားရှင်းအသစ်တင်ပေးပါ။', $lang));
            // }
            if ((int) $app_generation_number >= (int) $app_miniumn_generation_number) {
                return $next($request);
            }
        }

        return ResponseHelper::failedMessage(translateHelper::translate('You are using the old version of Starfish. Please update application.', 'လူကြီးမင်းသည် Starfish ဗားရှင်းအဟောင်းကိုအသုံးပြုနေပါသည်။ ကျေးဇူးပြု၍ ဗားရှင်းအသစ်တင်ပေးပါ။', $lang));
    }
}
