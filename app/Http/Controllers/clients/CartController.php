<?php
 
namespace App\Http\Controllers\clients;
use App\Http\Controllers\Controller;   
use Illuminate\Http\Request;


class CartController extends Controller
{
    public function index()
    {
        // // Giả sử bạn có một phương thức để lấy giỏ hàng của người dùng
        // $cartItems = session()->get('cart', []);
        
        return view('clients.carts.index');
    }

//     public function addToCart(Request $request, $productId)
//     {
//         // Thêm sản phẩm vào giỏ hàng
//         $cart = session()->get('cart', []);
        
//         if(isset($cart[$productId])) {
//             $cart[$productId]['quantity']++;
//         } else {
//             $cart[$productId] = [
//                 "name" => $request->name,
//                 "quantity" => 1,
//                 "price" => $request->price,
//             ];
//         }
        
//         session()->put('cart', $cart);
        
//         return redirect()->route('cart.index')->with('success', 'Sản phẩm đã được thêm vào giỏ hàng!');
//     }
}