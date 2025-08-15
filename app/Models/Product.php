<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'product_code',
        'product_name',
        'image',
        'description',
        'ingredients',
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

    public function getTotalVariantStockAttribute()
    {
        return $this->variants->sum(function ($v) {
            return $v->quantity_in_stock ?? 0;
        });
    }
    public function getTotalStockAttribute()
    {
        return $this->product_type === 'variant'
            ? $this->variants->sum('quantity_in_stock')
            : ($this->quantity_in_stock ?? $this->quantity ?? 0);
    }
    // app/Models/Product.php

    public function scopeAvailable($query)
    {
        $query->where('status', 1)
            ->where(function ($q) {
                $q->where(function ($simple) {
                    $simple->where('product_type', 'simple')
                        ->where('quantity_in_stock', '>', 0);
                })->orWhere(function ($variant) {
                    $variant->where('product_type', 'variant')
                        ->whereHas('variants', function ($q) {
                            $q->where('quantity_in_stock', '>', 0);
                        });
                });
            });
    }
    
}

