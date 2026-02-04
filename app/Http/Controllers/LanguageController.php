<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch(Request $request)
    {
        $locale = $request->input('locale', 'ar');

        if (in_array($locale, ['ar', 'en'])) {
            Session::put('locale', $locale);
            app()->setLocale($locale);
        }

        return redirect()->back();
    }
}
