<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }
    public function create()
    {
        $categories = Category::all(); 
        return view('admin.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'description' => 'nullable|string',
        ]);

        Category::create($request->all());

        return redirect()->route('categories.index')->with('success', 'Thêm danh mục thành công!');
    }

    public function show(string $id)
    {
        //
        $categories = Category::findOrFail($id);
        return view('admin.categories.show', compact('categories'));
    }


    public function edit(Category $category)
    {
        // Lấy tất cả danh mục khác với chính danh mục đang chỉnh sửa (tránh chọn làm cha chính nó)
        $parentCategories = Category::where('id', '!=', $category->id)->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Không thể xóa danh mục vì đang có sản phẩm liên kết.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Xóa danh mục thành công!');
    }
    
    public function toggleStatus(Category $category)
    {
        $category->status = !$category->status; 
        $category->save();

        return redirect()->route('categories.index')->with('success', 'Cập nhật trạng thái danh mục thành công!');
    }

}
