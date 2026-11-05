<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $categories = Category::all();

        return view('pages.admin.categories.index', compact('categories'));
    }

    public function store(CategoryRequest $request): \Illuminate\Http\RedirectResponse
    {
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Simpan gambar; database hanya menyimpan nama file
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        Category::create([
            'category_name' => $request->input('category_name'),
            'image' => $imagePath ? basename($imagePath) : null,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function update(CategoryRequest $request, Category $category): \Illuminate\Http\RedirectResponse
    {
        if ($request->hasFile('image')) {
            // Hapus gambar lama bila ada
            $this->deleteCategoryImage($category);

            $imagePath = $request->file('image')->store('categories', 'public');
            $category->image = basename($imagePath);
        }

        $category->update(['category_name' => $request->input('category_name')]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): \Illuminate\Http\RedirectResponse
    {
        $this->deleteCategoryImage($category);
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Hapus file gambar kategori bila ada di disk public.
     */
    private function deleteCategoryImage(Category $category): void
    {
        if ($category->image && Storage::exists('public/categories/'.$category->image)) {
            Storage::delete('public/categories/'.$category->image);
        }
    }
}
