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

        $query = Lending::with(['item', 'user'])->latest();

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('nama_peminjam')) {
            $query->where('nama_peminjam', 'like', '%' . $request->nama_peminjam . '%');
        }
        if ($request->filled('tanggal_pinjam_start')) {
            $query->whereDate('tanggal_pinjam', '>=', $request->tanggal_pinjam_start);
        }
        if ($request->filled('tanggal_pinjam_end')) {
            $query->whereDate('tanggal_pinjam', '<=', $request->tanggal_pinjam_end);
        }

        $lendings = $query->get();
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

        $request->validate([
            'returned_total' => 'required|integer|min:0',
            'damaged' => 'required|integer|min:0',
            'lost' => 'required|integer|min:0',
        ], [
            'returned_total.required' => 'Jumlah barang yang dikembalikan harus diisi!',
            'returned_total.integer' => 'Jumlah barang yang dikembalikan harus berupa angka.',
            'damaged.required' => 'Masukkan jumlah barang rusak.',
            'damaged.integer' => 'Jumlah barang rusak harus berupa angka.',
            'lost.required' => 'Masukkan jumlah barang hilang.',
            'lost.integer' => 'Jumlah barang hilang harus berupa angka.',
        ]);

        $returnedTotal = (int) $request->input('returned_total');
        $damaged = (int) $request->input('damaged');
        $lost = (int) $request->input('lost');
        $sumProcessed = $returnedTotal + $damaged + $lost;

        $alreadyProcessed = $lending->total_dikembalikan + $lending->rusak + $lending->hilang;
        $remaining = $lending->total - $alreadyProcessed;

        if ($returnedTotal > $remaining) {
            return redirect()->back()->withInput()->with('error', 'Jumlah barang dikembalikan tidak boleh lebih dari sisa peminjaman (' . $remaining . ').');
        }
        if ($damaged > $remaining) {
            return redirect()->back()->withInput()->with('error', 'Jumlah barang rusak tidak boleh lebih dari sisa peminjaman (' . $remaining . ').');
        }
        if ($lost > $remaining) {
            return redirect()->back()->withInput()->with('error', 'Jumlah barang hilang tidak boleh lebih dari sisa peminjaman (' . $remaining . ').');
        }
        if ($sumProcessed > $remaining) {
            return redirect()->back()->withInput()->with('error', 'Total pengembalian, rusak, dan hilang tidak boleh melebihi sisa peminjaman (' . $remaining . ').');
        }
        if ($sumProcessed <= 0) {
            return redirect()->back()->withInput()->with('error', 'Silakan masukkan minimal 1 barang yang dikembalikan, rusak, atau hilang.');
        }

        if ($lending->status === 'belum') {
            $lending->total_dikembalikan += $returnedTotal;
            $lending->rusak += $damaged;
            $lending->hilang += $lost;

            if ($lending->total_dikembalikan + $lending->rusak + $lending->hilang >= $lending->total) {
                $lending->status = 'sudah';
                $lending->tanggal_kembali = now()->toDateString();
            }

            $lending->save();

            $item = $lending->item;
            if ($item) {
                $item->peminjaman -= $sumProcessed;
                if ($item->peminjaman < 0) {
                    $item->peminjaman = 0;
                }

                if ($damaged > 0) {
                    $item->diperbaiki += $damaged;
                }

                if ($lost > 0) {
                    $item->total -= $lost;
                    if ($item->total < 0) {
                        $item->total = 0;
                    }
                }

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

        $query = Lending::with(['item', 'user'])->latest();
        $filterLabels = [];
        $filenameParts = [];

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
            $item = Item::find($request->item_id);
            $itemName = $item ? $item->nama : 'item-' . $request->item_id;
            $filterLabels[] = 'Item: ' . $itemName;
            $filenameParts[] = 'item_' . preg_replace('/[^A-Za-z0-9]+/', '_', strtolower($itemName));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
            $label = $request->status === 'sudah' ? 'Sudah dikembalikan' : 'Belum dikembalikan';
            $filterLabels[] = 'Status: ' . $label;
            $filenameParts[] = 'status_' . $request->status;
        }
        if ($request->filled('nama_peminjam')) {
            $query->where('nama_peminjam', 'like', '%' . $request->nama_peminjam . '%');
            $filterLabels[] = 'Nama: ' . $request->nama_peminjam;
            $filenameParts[] = 'nama_' . preg_replace('/[^A-Za-z0-9]+/', '_', strtolower($request->nama_peminjam));
        }
        if ($request->filled('tanggal_pinjam_start')) {
            $query->whereDate('tanggal_pinjam', '>=', $request->tanggal_pinjam_start);
            $filterLabels[] = 'Pinjam dari: ' . $request->tanggal_pinjam_start;
            $filenameParts[] = 'start_' . $request->tanggal_pinjam_start;
        }
        if ($request->filled('tanggal_pinjam_end')) {
            $query->whereDate('tanggal_pinjam', '<=', $request->tanggal_pinjam_end);
            $filterLabels[] = 'Pinjam sampai: ' . $request->tanggal_pinjam_end;
            $filenameParts[] = 'end_' . $request->tanggal_pinjam_end;
        }

        $lendings = $query->get();
        $filterTitle = count($filterLabels) ? implode(' | ', $filterLabels) : 'Semua data peminjaman';
        $filename = 'lendings_export_' . now()->format('Ymd') . (count($filenameParts) ? '_' . implode('_', $filenameParts) : '') . '.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function () use ($lendings, $filterTitle) {
            echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/></head><body>';
            echo '<table border="1" cellpadding="5" cellspacing="0">';
            echo '<tr>';
            echo '<td colspan="11" style="font-weight:bold; text-align:center; background:#f2f2f2;">' . htmlspecialchars($filterTitle, ENT_QUOTES, 'UTF-8') . '</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<th>#</th>';
            echo '<th>Item</th>';
            echo '<th>Total</th>';
            echo '<th>Nama Peminjam</th>';
            echo '<th>Tanggal Pinjam</th>';
            echo '<th>Tanggal Kembali</th>';
            echo '<th>Status</th>';
            echo '<th>Total Kembali</th>';
            echo '<th>Rusak</th>';
            echo '<th>Hilang</th>';
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
                echo '<td>' . htmlspecialchars($lending->total_dikembalikan ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($lending->rusak ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($lending->hilang ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') . '</td>';
                echo '</tr>';
            }

            echo '</table></body></html>';
        };

        return response()->stream($callback, 200, $headers);
    }
}
