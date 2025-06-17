<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

//  public function index()
//     {
//         $products = Product::with('category')
//             ->where('status', 1)
//             ->where('quantity', '>', 0)
//             ->paginate(6);

//         $user = Auth::user();
//         if ($user && $user->role && $user->role->name === 'admin') {
//             // dd($user->role->name);
//             return redirect('/admin/dashboard')
//                 ->with('error', 'Admin không được phép truy cập trang chủ.');
//         }
        
//         $products = Product::with('category')->where('status', 1)->paginate(12);
//         return view('clients.home', compact('products'));
//     }

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->role && $user->role->name === 'admin') {
            return redirect('/admin/dashboard')
                ->with('error', 'Admin không được phép truy cập trang chủ.');
        }

        $query = Product::with('category')
            ->where('status', 1)
            ->where('quantity', '>', 0);

        // Nếu có ?category=ID thì lọc theo danh mục
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->paginate(12);
        $categories = Category::withCount('products')->get();

        // Lấy tất cả danh mục và đếm số sản phẩm trong mỗi danh mục
        $bestSellingProducts = Product::with('category')
        ->where('status', 1)
        ->where('quantity', '>', 0)
        ->inRandomOrder() 
        ->take(8) 
        ->get();
 
        return view('clients.home', compact('products', 'categories', 'bestSellingProducts'));

    }

    
}
