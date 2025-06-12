<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProducClientController extends Controller
{
    public function index()
    {
        // Logic to fetch and display products
        return view('clients.product.index');
    }

  
}
?>