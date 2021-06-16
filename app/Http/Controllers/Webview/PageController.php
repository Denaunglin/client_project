<?php

namespace App\Http\Controllers\Webview;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function hotelPolicies(Request $request)
    {

        app()->setLocale($request->lang ?? 'en');

        return view('webview.policies');
    }

    public function aboutUs(Request $request)
    {
        app()->setLocale($request->lang ?? 'en');

        return view('webview.aboutus');
    }

    public function termsAndConditions(Request $request)
    {
        app()->setLocale($request->lang ?? 'en');

        return view('webview.terms_conditions');
    }
}
