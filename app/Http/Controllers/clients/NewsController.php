<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        return view('clients.news');
    }

    public function detail($id)
{
    $viewPath = 'clients.news.news' . $id;

    if (view()->exists($viewPath)) {
        return view($viewPath);
    }

    abort(404);
}
}
