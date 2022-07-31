<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * HomeController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth')->only('index');
    }

    /**
     * Acceuil
     *
     * @return mixed
     */
    public function index()
    {
        return view('home');
    }

}
