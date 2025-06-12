// 1. Migration: database/migrations/xxxx_xx_xx_create_comments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('comments');
    }
};


// 2. Model: app/Models/Comment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id', 'content'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}


// 3. ProductController methods
// app/Http/Controllers/ProductController.php
use App\Models\Product;
use App\Models\Comment;
use Illuminate\Http\Request;

public function show($id)
{
    $product = Product::with(['comments.user'])->findOrFail($id);
    return view('client.product_detail', compact('product'));
}

public function comment(Request $request, $id)
{
    $request->validate([
        'content' => 'required|string|max:1000',
    ]);

    Comment::create([
        'user_id' => auth()->id(),
        'product_id' => $id,
        'content' => $request->input('content'),
    ]);

    return back()->with('success', 'Bình luận đã được gửi.');
}


// 4. Admin Comment Controller: app/Http/Controllers/Admin/CommentAdminController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentAdminController extends Controller
{
    public function index()
    {
        $comments = Comment::with(['product', 'user'])->latest()->paginate(10);
        return view('admin.comments.index', compact('comments'));
    }

    public function destroy($id)
    {
        Comment::destroy($id);
        return back()->with('success', 'Đã xóa bình luận.');
    }
}


// 5. Web routes: routes/web.php
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\CommentAdminController;

Route::post('/product/{id}/comment', [ProductController::class, 'comment'])->middleware('auth');

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/comments', [CommentAdminController::class, 'index']);
    Route::delete('/admin/comments/{id}', [CommentAdminController::class, 'destroy']);
});


// 6. View: resources/views/client/product_detail.blade.php
<h4>Bình luận:</h4>
@auth
<form action="/product/{{ $product->id }}/comment" method="POST">
    @csrf
    <textarea name="content" class="form-control mb-2" rows="3" required></textarea>
    <button class="btn btn-primary">Gửi bình luận</button>
</form>
@else
<p><a href="/login">Đăng nhập</a> để bình luận</p>
@endauth

@foreach ($product->comments as $comment)
    <div class="border p-2 my-2">
        <strong>{{ $comment->user->name }}</strong>:
        <p>{{ $comment->content }}</p>
        <small>{{ $comment->created_at->diffForHumans() }}</small>
    </div>
@endforeach


// 7. View: resources/views/admin/comments/index.blade.php
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Sản phẩm</th>
            <th>Người dùng</th>
            <th>Nội dung</th>
            <th>Ngày</th>
            <th>Xóa</th>
        </tr>
    </thead>
    <tbody>
        @foreach($comments as $comment)
        <tr>
            <td>{{ $comment->product->name }}</td>
            <td>{{ $comment->user->name }}</td>
            <td>{{ $comment->content }}</td>
            <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
            <td>
                <form action="/admin/comments/{{ $comment->id }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
