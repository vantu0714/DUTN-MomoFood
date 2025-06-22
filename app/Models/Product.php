<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo nếu chưa có

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'product_name',
        'image',
        'description',
        'ingredients',
        'expiration_date',
        'original_price',
        'discounted_price',
        'status', // Cột này sẽ dùng để kiểm tra trạng thái "Còn hàng" / "Hết hàng"
        'view',
        'is_show_home',
        'category_id',
        'quantity',
        'product_type',
    ];
    public function category(): BelongsTo // Khuyến nghị thêm kiểu trả về để code rõ ràng hơn
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * Accessor: Lấy trạng thái còn hàng của sản phẩm dưới dạng boolean.
     * Sử dụng cột 'status' của bạn.
     * Ví dụ: Nếu 'status' là 'Còn hàng', thì trả về true.
     *
     * @return bool
     */
    public function getIsAvailableAttribute(): bool
    {
        // Giả sử cột 'status' của bạn lưu chuỗi 'Còn hàng' hoặc 'Hết hàng'
        return $this->status === 'Còn hàng';
    }

    /**
     * Accessor: Lấy trạng thái của sản phẩm dưới dạng text (nếu bạn muốn chuẩn hóa cách hiển thị).
     *
     * @return string
     */
    public function getAvailabilityStatusTextAttribute(): string
    {
        // Điều này giúp bạn dễ dàng hiển thị đúng trạng thái trong Blade
        return $this->status; // Trả về trực tiếp giá trị từ cột 'status'
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function index()
    {
        $products = Product::with('category')->paginate(10); // pagination
        $totalProducts = Product::count(); // đếm toàn bộ sản phẩm (không phân trang)

        return view('admin.products.index', compact('products', 'totalProducts'));
    }
    public function orderItems()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function comments()
{
    return $this->hasMany(Comment::class);
}
}
