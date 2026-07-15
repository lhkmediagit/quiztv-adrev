<?php

namespace App\Controllers;

/**
 * Controller: InfoController
 * Manages static/informational pages like About, Contact, Privacy, Terms, and Disclaimer.
 */
class InfoController extends BaseController
{
    public function about()
    {
        return view('info/about', ['title' => 'About Us - QuizTv']);
    }

    public function contact()
    {
        return view('info/contact', ['title' => 'Contact Us - QuizTv']);
    }

    public function privacy()
    {
        return view('info/privacy', ['title' => 'Privacy Policy - QuizTv']);
    }

    public function terms()
    {
        return view('info/terms', ['title' => 'Terms of Use - QuizTv']);
    }

    public function disclaimer()
    {
        return view('info/disclaimer', ['title' => 'Disclaimer - QuizTv']);
    }
}
