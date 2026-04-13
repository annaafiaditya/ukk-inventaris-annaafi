<?php

namespace App\Http\Controllers;

use App\Models\Lending;
use App\Models\Item;
use Illuminate\Http\Request;

class LendingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'staff') {
            abort(403);
        }

        $lendings = Lending::with(['item', 'user'])->latest()->get();
        $items = Item::all();
        
        return view('lendings.index', compact('lendings', 'items'));
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'staff') {
            abort(403);
        }

        $request->validate([
            'nama_peminjam' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.total' => 'required|integer|min:1',
        ], [
            'items.required' => 'Pilih minimal 1 item untuk dipinjam!',
            'nama_peminjam.required' => 'Nama peminjam harus diisi!',
        ]);

        // Validate availability
        foreach ($request->items as $borrow) {
            $item = Item::findOrFail($borrow['item_id']);
            $tersedia = $item->total - $item->diperbaiki - $item->peminjaman;
            if ($borrow['total'] > $tersedia) {
                return redirect()->back()->withInput()->with('error', 'Total peminjaman untuk ' . $item->nama . ' melebihi stok tersedia (' . $tersedia . ')!');
            }
        }

        // Insert lendings & update item peminjaman
        foreach ($request->items as $borrow) {
            $item = Item::find($borrow['item_id']);
            
            Lending::create([
                'nama_peminjam' => $request->nama_peminjam,
                'item_id' => $borrow['item_id'],
                'total' => $borrow['total'],
                'keterangan' => $request->keterangan,
                'tanggal_pinjam' => now()->toDateString(),
                'status' => 'belum',
                'user_id' => $request->user()->id,
            ]);

            $item->peminjaman += $borrow['total'];
            $item->save();
        }

        return redirect()->route('lendings.index')->with('success', 'Peminjaman berhasil ditambahkan!');
    }

    public function returnItem(Request $request, Lending $lending)
    {
        if ($request->user()->role !== 'staff') {
            abort(403);
        }

        if ($lending->status === 'belum') {
            $lending->status = 'sudah';
            $lending->tanggal_kembali = now()->toDateString();
            $lending->save();

            // Kembalikan ke tersedia
            $item = $lending->item;
            if ($item) {
                $item->peminjaman -= $lending->total;
                if ($item->peminjaman < 0) $item->peminjaman = 0;
                $item->save();
            }
        }

        return redirect()->back()->with('success', 'Peminjaman berhasil dikembalikan!');
    }

    public function destroy(Request $request, Lending $lending)
    {
        if ($request->user()->role !== 'staff') {
            abort(403);
        }

        // If it hasn't been returned, return the item's quantity back to available first
        if ($lending->status === 'belum') {
            $item = $lending->item;
            if ($item) {
                $item->peminjaman -= $lending->total;
                if ($item->peminjaman < 0) $item->peminjaman = 0;
                $item->save();
            }
        }

        $lending->delete();

        return redirect()->back()->with('success', 'Data peminjaman berhasil dihapus!');
    }

    public function export(Request $request)
    {
        if ($request->user()->role !== 'staff') {
            abort(403);
        }

        $lendings = Lending::with(['item', 'user'])->latest()->get();
        $filename = 'lendings_export_' . now()->format('Ymd') . '.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function () use ($lendings) {
            echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/></head><body>';
            echo '<table border="1" cellpadding="5" cellspacing="0">';
            echo '<tr>';
            echo '<th>#</th>';
            echo '<th>Item</th>';
            echo '<th>Total</th>';
            echo '<th>Nama Peminjam</th>';
            echo '<th>Tanggal Pinjam</th>';
            echo '<th>Tanggal Kembali</th>';
            echo '<th>Status</th>';
            echo '<th>Edit Oleh</th>';
            echo '</tr>';

            foreach ($lendings as $index => $lending) {
                $itemName = $lending->item->nama ?? '-';
                $total = $lending->total > 0 ? $lending->total : '-';
                $borrower = $lending->nama_peminjam ?: '-';
                $tanggalPinjam = $lending->tanggal_pinjam ? $lending->tanggal_pinjam : '-';
                $tanggalKembali = $lending->tanggal_kembali ? $lending->tanggal_kembali : '-';
                $status = $lending->status ?: '-';
                $userName = $lending->user->name ?? '-';

                echo '<tr>';
                echo '<td>' . ($index + 1) . '</td>';
                echo '<td>' . htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($total, ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($borrower, ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($tanggalPinjam, ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($tanggalKembali, ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') . '</td>';
                echo '</tr>';
            }

            echo '</table></body></html>';
        };

        return response()->stream($callback, 200, $headers);
    }
}
