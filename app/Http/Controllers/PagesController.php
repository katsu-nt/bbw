<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function showPrivacyPolicy()
    {
        return view('privacy_policy');
    }
    public function showStructure()
    {
        return view('show_structure');
    }
    public function showTermsAndConditions()
    {
        return view('terms_and_conditions');
    }
}