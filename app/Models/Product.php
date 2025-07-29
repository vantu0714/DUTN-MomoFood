<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'status',
        'view',
        'is_show_home',
        'category_id',
        'quantity_in_stock',
        'product_type',
        'origin_id',
    ];
    protected $casts = [
        'expiration_date' => 'date',
    ];

    public function category(): BelongsTo
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
        return $this->status === 1;
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


    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_id');
    }

    public function comboItems()
    {
        return $this->hasMany(comboItem::class, 'combo_id');
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    protected static function booted()
    {
        static::deleting(function ($product) {
            // Nếu là combo
            if ($product->is_combo) {
                // Nếu có trong đơn hàng thì không cho xóa
                if ($product->orderDetails()->exists()) {
                    throw new \Exception("Không thể xóa combo đã tồn tại trong đơn hàng.");
                }

                // Xóa các combo_items liên quan
                $product->comboItems()->delete();
            }
        });
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }


    public function orderItems()
    {
        return $this->hasMany(OrderDetail::class, 'product_id');
    }
    public function getDisplayPriceAttribute()
    {
        return $this->original_price ?? $this->variants()->min('price');
    }
    public function origin()
    {
        return $this->belongsTo(ProductOrigin::class, 'origin_id');
    }
}
