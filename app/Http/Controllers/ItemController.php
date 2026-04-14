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
            'hilang' => 0,
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
            'rusak' => 'nullable|integer|min:0',
            'sudah_diperbaiki' => 'nullable|integer|min:0',
            'hilang' => 'nullable|integer|min:0',
            'ketemu' => 'nullable|integer|min:0',
        ]);

        $requestedTotal = (int) $request->input('total', 0);
        $rusak = (int) $request->input('rusak', 0);
        $sudahDiperbaiki = (int) $request->input('sudah_diperbaiki', 0);
        $hilang = (int) $request->input('hilang', 0);
        $ketemu = (int) $request->input('ketemu', 0);
        $available = $item->total - $item->diperbaiki - $item->peminjaman;

        if ($rusak > $available) {
            return redirect()->back()->withInput()->with('error', 'Jumlah barang rusak tidak boleh lebih besar dari stok tersedia (' . $available . ').');
        }
        if ($sudahDiperbaiki > $item->diperbaiki) {
            return redirect()->back()->withInput()->with('error', 'Jumlah barang yang sudah diperbaiki tidak boleh lebih besar dari yang rusak saat ini (' . $item->diperbaiki . ').');
        }
        if ($hilang > $requestedTotal) {
            return redirect()->back()->withInput()->with('error', 'Jumlah barang hilang tidak boleh lebih besar dari total yang diinput (' . $requestedTotal . ').');
        }
        if ($ketemu > $item->hilang) {
            return redirect()->back()->withInput()->with('error', 'Jumlah barang ketemu tidak boleh lebih besar dari jumlah hilang saat ini (' . $item->hilang . ').');
        }

        $item->nama = $request->nama;
        $item->category_id = $request->category_id;
        $item->total = $requestedTotal;

        if ($rusak > 0) {
            $item->diperbaiki += $rusak;
        }

        if ($sudahDiperbaiki > 0) {
            $item->diperbaiki -= $sudahDiperbaiki;
            if ($item->diperbaiki < 0) {
                $item->diperbaiki = 0;
            }
            $item->total += $sudahDiperbaiki;
        }

        if ($hilang > 0) {
            $item->total -= $hilang;
            if ($item->total < 0) {
                $item->total = 0;
            }
            $item->hilang += $hilang;
        }

        if ($ketemu > 0) {
            $item->hilang -= $ketemu;
            if ($item->hilang < 0) {
                $item->hilang = 0;
            }
            $item->total += $ketemu;
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
                    <th>Lost Total</th>
                    <th>Last Updated</th>
                  </tr>';

            foreach ($items as $item) {
                echo '<tr>';
                echo '<td>' . ($item->category->nama_kategori ?? '-') . '</td>';
                echo '<td>' . ($item->nama ?? '-') . '</td>';
                echo '<td>' . ($item->total ?? '-') . '</td>';
                echo '<td>' . ($item->diperbaiki ?? '-') . '</td>';
                echo '<td>' . ($item->hilang ?? 0) . '</td>';
                echo '<td>' . ($item->updated_at ? $item->updated_at->format('M d, Y') : '-') . '</td>';
                echo '</tr>';
            }

            echo '</table></body></html>';
        };

        return response()->stream($callback, 200, $headers);
    }
}