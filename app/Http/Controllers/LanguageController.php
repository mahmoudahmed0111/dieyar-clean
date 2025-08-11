<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switchLanguage($locale)
    {
        // التحقق من أن اللغة مدعومة
        $supportedLocales = ['ar', 'en'];

        if (in_array($locale, $supportedLocales)) {
            Session::put('locale', $locale);
            App::setLocale($locale);
        }

        return redirect()->back();
    }
}
