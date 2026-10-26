<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class gudangController extends Controller
{
    public function index(){
        return view('gudang.dashboard');
    }
}
