<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

 public function index()
    {
        $products = Product::with('category')
            ->where('status', 1)
            ->where('quantity', '>', 0)
            ->paginate(6);

        $user = Auth::user();
        if ($user && $user->role && $user->role->name === 'admin') {
            // dd($user->role->name);
            return redirect('/admin/dashboard')
                ->with('error', 'Admin không được phép truy cập trang chủ.');
        }
        $products = Product::with('category')->where('status', 1)->paginate(12);
        return view('clients.home', compact('products'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
