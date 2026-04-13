<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $categories = Category::with('items')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'divisi_pj' => ['required', Rule::in(['sarpras', 'tata usaha', 'tefa'])],
        ]);

        Category::create([
            'nama_kategori' => $request->nama_kategori,
            'divisi_pj' => $request->divisi_pj,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Category $category)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'divisi_pj' => ['required', Rule::in(['sarpras', 'tata usaha', 'tefa'])],
        ]);

        $category->update([
            'nama_kategori' => $request->nama_kategori,
            'divisi_pj' => $request->divisi_pj,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Category $category)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus!');
    }
}
    