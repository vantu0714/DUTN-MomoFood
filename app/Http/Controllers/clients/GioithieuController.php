<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GioithieuController extends Controller
{
    public function index()
    {
        return view('clients.gioithieu');
    }
}