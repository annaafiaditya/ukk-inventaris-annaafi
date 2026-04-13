<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin' && $request->user()->role !== 'staff') {
            abort(403);
        }

        $items = Item::with(['category', 'lendings'])->get();
        $categories = Category::all();

        return view('items.index', compact('items', 'categories'));
    }

    public function showLendings(Request $request, Item $item)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $lendings = $item->lendings()->with('user')->latest()->get();
        return view('items.lendings', compact('item', 'lendings'));
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'total' => 'required|integer|min:0',
        ]);

        Item::create([
            'nama' => $request->nama,
            'category_id' => $request->category_id,
            'total' => $request->total,
            'diperbaiki' => 0,
            'peminjaman' => 0,
        ]);

        return redirect()->route('items.index')->with('success', 'Item berhasil ditambahkan!');
    }

    public function edit(Request $request, Item $item)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'total' => 'required|integer|min:0',
            'baru_rusak' => 'nullable|integer|min:0',
        ]);

        $item->nama = $request->nama;
        $item->category_id = $request->category_id;
        $item->total = $request->total;

        if ($request->filled('baru_rusak')) {
            $item->diperbaiki += $request->baru_rusak;
        }

        $item->save();

        return redirect()->route('items.index')->with('success', 'Item berhasil diperbarui!');
    }

    public function destroy(Request $request, Item $item)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item berhasil dihapus!');
    }

    public function export(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $items = Item::with('category')->get();

        $filename = 'items_export_' . now()->format('Ymd') . '.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($items) {
            echo '<html><head><meta charset="UTF-8"></head><body>';
            echo '<table border="1" cellpadding="5" cellspacing="0">';
            
            echo '<tr>
                    <th>Category</th>
                    <th>Name Item</th>
                    <th>Total</th>
                    <th>Repair Total</th>
                    <th>Last Updated</th>
                  </tr>';

            foreach ($items as $item) {
                echo '<tr>';
                echo '<td>' . ($item->category->nama_kategori ?? '-') . '</td>';
                echo '<td>' . ($item->nama ?? '-') . '</td>';
                echo '<td>' . ($item->total ?? '-') . '</td>';
                echo '<td>' . ($item->diperbaiki ?? '-') . '</td>';
                echo '<td>' . ($item->updated_at ? $item->updated_at->format('M d, Y') : '-') . '</td>';
                echo '</tr>';
            }

            echo '</table></body></html>';
        };

        return response()->stream($callback, 200, $headers);
    }
}