<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Peminjaman Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Kesalahan!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Kesalahan validasi:</strong>
                <ul class="list-disc list-inside mt-2 text-sm">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b border-gray-200 font-semibold text-lg border-l-4 border-blue-500">
                    Tambah Peminjaman
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('lendings.store') }}" x-data="lendingForm()" id="borrowForm">
                        @csrf
                        <input type="hidden" name="signature_staff" id="borrow_signature_staff" />
                        <input type="hidden" name="signature_borrower" id="borrow_signature_borrower" />
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="nama_peminjam" value="Nama Peminjam" />
                                <x-text-input id="nama_peminjam" class="block mt-1 w-full" type="text" name="nama_peminjam" :value="old('nama_peminjam')" required />
                                <x-input-error :messages="$errors->get('nama_peminjam')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="keterangan" value="Keterangan" />
                                <x-text-input id="keterangan" class="block mt-1 w-full" type="text" name="keterangan" :value="old('keterangan')" />
                                <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                            </div>
                        </div>

                        <div class="border rounded-md p-4 mb-4 bg-gray-50">
                            <div class="font-semibold text-md mb-4 text-gray-700 flex justify-between items-center">
                                <span>Daftar Barang yang Dipinjam</span>
                                <button type="button" @click="addItem()" class="bg-blue-600 font-bold text-white px-3 py-1 rounded text-sm">
                                    + Tambah Barang
                                </button>
                            </div>
                            
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex items-end gap-4 mb-4 pb-4 border-b border-gray-200">
                                    <div class="flex-1">
                                        <x-input-label value="Pilih Item" />
                                        <select x-bind:name="'items[' + index + '][item_id]'" x-model="item.item_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                            <option value="" disabled selected>Pilih Item</option>
                                            @foreach($items as $dbItem)
                                            <option value="{{ $dbItem->id }}">{{ $dbItem->nama }} (Tersedia: {{ $dbItem->total - $dbItem->diperbaiki - $dbItem->peminjaman }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-32">
                                        <x-input-label value="Total" />
                                        <x-text-input x-bind:name="'items[' + index + '][total]'" x-model="item.total" class="block mt-1 w-full" type="number" min="1" required />
                                    </div>
                                    <div class="w-auto">
                                        <button type="button" @click="removeItem(index)" class="text-red-600 font-bold p-2 mb-1 border rounded">
                                            X
                                        </button>
                                    </div>
                                </div>
                            </template>
                            
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button type="button" onclick="openSignatureModal('borrow', 'borrowForm', document.getElementById('nama_peminjam').value)" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Simpan Peminjaman
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200 font-semibold text-lg border-l-4 border-indigo-500">
                    Daftar Peminjaman
                </div>
                <div class="p-6 overflow-x-auto">
                    <form method="GET" action="{{ route('lendings.index') }}" class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <x-input-label for="item_id" value="Filter Item" />
                            <select id="item_id" name="item_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Semua Item</option>
                                @foreach($items as $dbItem)
                                <option value="{{ $dbItem->id }}" {{ request('item_id') == $dbItem->id ? 'selected' : '' }}>{{ $dbItem->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="status" value="Filter Status" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Semua Status</option>
                                <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum</option>
                                <option value="sudah" {{ request('status') == 'sudah' ? 'selected' : '' }}>Sudah</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="nama_peminjam" value="Nama Peminjam" />
                            <x-text-input id="nama_peminjam" name="nama_peminjam" class="block mt-1 w-full" type="text" value="{{ request('nama_peminjam') }}" />
                        </div>
                        <div>
                            <x-input-label for="tanggal_pinjam_start" value="Tanggal Pinjam Dari" />
                            <x-text-input id="tanggal_pinjam_start" name="tanggal_pinjam_start" class="block mt-1 w-full" type="date" value="{{ request('tanggal_pinjam_start') }}" />
                        </div>
                        <div>
                            <x-input-label for="tanggal_pinjam_end" value="Tanggal Pinjam Sampai" />
                            <x-text-input id="tanggal_pinjam_end" name="tanggal_pinjam_end" class="block mt-1 w-full" type="date" value="{{ request('tanggal_pinjam_end') }}" />
                        </div>
                        <div class="flex gap-2">
                            <x-primary-button class="w-full">
                                Filter
                            </x-primary-button>
                            <a href="{{ route('lendings.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                Reset
                            </a>
                        </div>
                    </form>
                    <div class="mb-4 flex justify-end">
                        <a href="{{ route('lendings.export') }}{{ request()->getQueryString() ? ('?' . request()->getQueryString()) : '' }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Export Excel
                        </a>
                    </div>
                    <table class="w-full text-left table-auto">
                        <thead>
                            <tr>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm">#</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm">Item</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm">Total</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm">Nama</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm text-center">Tanggal</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm text-center">Pengembalian</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm text-center">Total Kembali</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm text-center">Rusak</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm text-center">Hilang</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm">Edit Oleh</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lendings as $lending)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4 border-b border-blue-gray-50 text-sm">{{ $loop->iteration }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm font-semibold">{{ $lending->item->nama ?? 'Item terhapus' }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm text-center font-bold">{{ $lending->total }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm">{{ $lending->nama_peminjam }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm text-center">{{ \Carbon\Carbon::parse($lending->tanggal_pinjam)->format('d M Y') }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm text-center">
                                    @if($lending->status === 'sudah')
                                        <span class="inline-block px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                            {{ \Carbon\Carbon::parse($lending->tanggal_kembali)->format('d M Y') }}
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                            Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm text-center">{{ $lending->total_dikembalikan ?? '-' }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm text-center">{{ $lending->rusak ?? '-' }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm text-center">{{ $lending->hilang ?? '-' }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm">{{ $lending->user->name ?? 'Terhapus' }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm text-right">
                                    @if($lending->status === 'belum')
                                    <div x-data="{ open: false }" class="space-y-2">
                                        <button type="button" @click="open = !open" class="text-xs bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-3 rounded drop-shadow transition">
                                            Pengembalian
                                        </button>
                                        <div x-show="open" x-cloak class="mt-2 rounded-md border border-gray-200 bg-gray-50 p-3 text-sm max-w-[420px] w-full">
                                            <form action="{{ route('lendings.return', $lending) }}" method="POST" class="space-y-3" id="returnForm_{{ $lending->id }}">
                                                @csrf
                                                <input type="hidden" name="signature_staff" id="return_signature_staff_{{ $lending->id }}" />
                                                <input type="hidden" name="signature_borrower" id="return_signature_borrower_{{ $lending->id }}" />
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 max-w-full">
                                                    <div>
                                                        <x-input-label for="returned_total_{{ $lending->id }}" value="Total Kembali" />
                                                        <x-text-input id="returned_total_{{ $lending->id }}" name="returned_total" class="block mt-1 w-full" type="number" min="0" value="{{ old('returned_total', 0) }}" required />
                                                    </div>
                                                    <div>
                                                        <x-input-label for="damaged_{{ $lending->id }}" value="Rusak" />
                                                        <x-text-input id="damaged_{{ $lending->id }}" name="damaged" class="block mt-1 w-full" type="number" min="0" value="{{ old('damaged', 0) }}" required />
                                                    </div>
                                                    <div>
                                                        <x-input-label for="lost_{{ $lending->id }}" value="Hilang" />
                                                        <x-text-input id="lost_{{ $lending->id }}" name="lost" class="block mt-1 w-full" type="number" min="0" value="{{ old('lost', 0) }}" required />
                                                    </div>
                                                </div>
                                                <div class="flex flex-wrap gap-2 justify-end">
                                                    <button type="button" onclick="openSignatureModal('return', 'returnForm_{{ $lending->id }}', '{{ addslashes($lending->nama_peminjam) }}')" class="text-xs bg-green-600 hover:bg-green-700 text-white font-semibold py-1 px-3 rounded transition">Simpan</button>
                                                    <button type="button" @click="open = false" class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-1 px-3 rounded transition">Batal</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @endif
                                    <form action="{{ route('lendings.destroy', $lending) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-white hover:bg-red-600 border border-red-600 font-semibold py-1 px-3 rounded transition" onclick="return confirm('Hapus data peminjaman ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="p-4 text-center text-gray-500">Belum ada data peminjaman.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div id="signatureModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-4xl rounded-xl bg-white shadow-xl overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold" id="signatureHeading">Tanda Tangan Peminjaman</h3>
                    <p class="text-sm text-gray-600" id="signatureModeLabel">Isi tanda tangan untuk konfirmasi.</p>
                    <div class="mt-3 rounded-md bg-gray-50 border border-gray-200 p-3 text-sm text-gray-700" id="signatureDetails">Informasi peminjaman/pengembalian akan muncul di sini.</div>
                </div>
                <button type="button" onclick="closeSignatureModal()" class="text-gray-500 hover:text-gray-700">Tutup</button>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="border rounded-lg p-4">
                        <div class="mb-2 text-sm font-semibold">Staff</div>
                        <div class="mb-2 text-sm text-gray-600" id="staffName">{{ Auth::user()->name }}</div>
                        <canvas id="staffCanvas" width="700" height="160" class="w-full h-40 border rounded-md bg-white"></canvas>
                        <button type="button" onclick="clearCanvas('staffCanvas')" class="mt-3 inline-flex items-center px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Hapus</button>
                    </div>
                    <div class="border rounded-lg p-4">
                        <div class="mb-2 text-sm font-semibold">Peminjam</div>
                        <div class="mb-2 text-sm text-gray-600" id="borrowerName">-</div>
                        <canvas id="borrowerCanvas" width="700" height="160" class="w-full h-40 border rounded-md bg-white"></canvas>
                        <button type="button" onclick="clearCanvas('borrowerCanvas')" class="mt-3 inline-flex items-center px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Hapus</button>
                    </div>
                </div>
                <div>
                    <div id="signatureError" class="hidden rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700"></div>
                </div>
                <div class="flex flex-wrap gap-3 justify-end">
                    <button type="button" onclick="submitSignatureAndPdf()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Simpan & Download PDF</button>
                    <button type="button" onclick="closeSignatureModal()" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        let activeSignatureFormId = null;
        let activeSignatureMode = 'borrow';
        let activeBorrowerName = '-';
        let activeSignatureDetails = '';
        let blankStaffSignature = null;
        let blankBorrowerSignature = null;

        function lendingForm() {
            return {
                items: [
                    { item_id: '', total: 1 }
                ],
                addItem() {
                    this.items.push({ item_id: '', total: 1 });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                }
            }
        }

        function openSignatureModal(mode, formId, borrowerName) {
            activeSignatureFormId = formId;
            activeSignatureMode = mode;
            activeBorrowerName = borrowerName || '-';
            document.getElementById('borrowerName').innerText = activeBorrowerName;
            document.getElementById('signatureHeading').innerText = mode === 'borrow' ? 'Tanda Tangan Peminjaman' : 'Tanda Tangan Pengembalian';
            document.getElementById('signatureModeLabel').innerText = mode === 'borrow' ? 'Tanda tangan untuk menyetujui peminjaman.' : 'Tanda tangan untuk menyetujui pengembalian.';
            activeSignatureDetails = getSignatureDetails(mode, formId);
            document.getElementById('signatureDetails').innerText = activeSignatureDetails;
            document.getElementById('signatureError').classList.add('hidden');
            clearCanvas('staffCanvas');
            clearCanvas('borrowerCanvas');
            document.getElementById('signatureModal').classList.remove('hidden');
        }

        function closeSignatureModal() {
            document.getElementById('signatureModal').classList.add('hidden');
        }

        function initializeSignatures() {
            const staffCanvas = document.getElementById('staffCanvas');
            const borrowerCanvas = document.getElementById('borrowerCanvas');
            setupCanvas(staffCanvas);
            setupCanvas(borrowerCanvas);
            blankStaffSignature = staffCanvas.toDataURL('image/png');
            blankBorrowerSignature = borrowerCanvas.toDataURL('image/png');
        }

        function setupCanvas(canvas) {
            const ratio = window.devicePixelRatio || 1;
            const width = 700;
            const height = 160;
            canvas.width = width * ratio;
            canvas.height = height * ratio;
            canvas.style.width = '100%';
            canvas.style.height = '160px';
            const ctx = canvas.getContext('2d');
            ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
            ctx.lineJoin = 'round';
            ctx.lineCap = 'round';
            ctx.lineWidth = 2;
            ctx.strokeStyle = '#000000';
            attachCanvasEvents(canvas, ctx);
        }

        function attachCanvasEvents(canvas, ctx) {
            let drawing = false;
            let lastX = 0;
            let lastY = 0;

            function getPos(event) {
                const rect = canvas.getBoundingClientRect();
                const clientX = event.touches ? event.touches[0].clientX : event.clientX;
                const clientY = event.touches ? event.touches[0].clientY : event.clientY;
                return {
                    x: clientX - rect.left,
                    y: clientY - rect.top,
                };
            }

            canvas.addEventListener('pointerdown', (event) => {
                drawing = true;
                const pos = getPos(event);
                lastX = pos.x;
                lastY = pos.y;
            });
            canvas.addEventListener('pointermove', (event) => {
                if (!drawing) return;
                const pos = getPos(event);
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
                lastX = pos.x;
                lastY = pos.y;
            });
            canvas.addEventListener('pointerup', () => drawing = false);
            canvas.addEventListener('pointerleave', () => drawing = false);
            canvas.addEventListener('touchstart', (event) => event.preventDefault(), { passive: false });
        }

        function clearCanvas(id) {
            const canvas = document.getElementById(id);
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function isBlankSignature(canvas, blankData) {
            return canvas.toDataURL('image/png') === blankData;
        }

        function submitSignatureAndPdf() {
            const staffCanvas = document.getElementById('staffCanvas');
            const borrowerCanvas = document.getElementById('borrowerCanvas');
            const staffDataUrl = staffCanvas.toDataURL('image/png');
            const borrowerDataUrl = borrowerCanvas.toDataURL('image/png');

            if (isBlankSignature(staffCanvas, blankStaffSignature)) {
                return showSignatureError('Tanda tangan staff belum diisi.');
            }
            if (isBlankSignature(borrowerCanvas, blankBorrowerSignature)) {
                return showSignatureError('Tanda tangan peminjam belum diisi.');
            }

            const form = document.getElementById(activeSignatureFormId);
            if (!form) {
                return showSignatureError('Form tidak ditemukan.');
            }

            const staffField = form.querySelector('[name="signature_staff"]');
            const borrowerField = form.querySelector('[name="signature_borrower"]');
            if (staffField) staffField.value = staffDataUrl;
            if (borrowerField) borrowerField.value = borrowerDataUrl;

            generatePdf(staffDataUrl, borrowerDataUrl);

            setTimeout(() => {
                form.submit();
            }, 300);
        }

        function showSignatureError(message) {
            const errorEl = document.getElementById('signatureError');
            errorEl.innerText = message;
            errorEl.classList.remove('hidden');
        }

        function getSignatureDetails(mode, formId) {
            const form = document.getElementById(formId);
            if (!form) {
                return 'Detail form tidak ditemukan.';
            }

            const lines = [];
            if (mode === 'borrow') {
                lines.push('Mode: Peminjaman');
                lines.push('Nama Peminjam: ' + (document.getElementById('nama_peminjam')?.value || '-'));
                const keterangan = form.querySelector('[name="keterangan"]')?.value || '-';
                lines.push('Keterangan: ' + keterangan);
                lines.push('Daftar Barang:');

                const selects = Array.from(form.querySelectorAll('select[name$="[item_id]"]'));
                if (selects.length === 0) {
                    lines.push('- Tidak ada item');
                } else {
                    selects.forEach((select) => {
                        const match = select.name.match(/items\[(\d+)\]\[item_id\]/);
                        const index = match ? match[1] : null;
                        const itemText = select.options[select.selectedIndex]?.text || '-';
                        const qtyInput = index !== null ? form.querySelector('input[name="items[' + index + '][total]"]') : null;
                        const qty = qtyInput?.value || '-';
                        lines.push('- ' + itemText + ' (Jumlah: ' + qty + ')');
                    });
                }
            } else {
                lines.push('Mode: Pengembalian');
                lines.push('Nama Peminjam: ' + activeBorrowerName);
                const returned = form.querySelector('[name="returned_total"]')?.value || '0';
                const damaged = form.querySelector('[name="damaged"]')?.value || '0';
                const lost = form.querySelector('[name="lost"]')?.value || '0';
                lines.push('Total yang Dikembalikan: ' + returned);
                lines.push('Rusak: ' + damaged);
                lines.push('Hilang: ' + lost);
                const note = form.querySelector('[name="return_note"]')?.value || '-';
                if (note !== '-') {
                    lines.push('Catatan: ' + note);
                }
            }

            return lines.join('\n');
        }

        function generatePdf(staffImage, borrowerImage) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('landscape', 'mm', 'a4');
            doc.setFontSize(14);
            const title = activeSignatureMode === 'borrow' ? 'TANDA TANGAN PEMINJAMAN' : 'TANDA TANGAN PENGEMBALIAN';
            doc.text(title, 14, 16);
            doc.setFontSize(10);
            doc.text('Staff: {{ Auth::user()->name }}', 14, 26);
            doc.text('Peminjam: ' + activeBorrowerName, 14, 32);
            const detailLines = activeSignatureDetails.split('\n');
            let y = 38;
            detailLines.forEach((line) => {
                doc.text(line, 14, y);
                y += 6;
            });
            const imageY = y + 4;
            doc.addImage(staffImage, 'PNG', 14, imageY, 120, 40);
            doc.addImage(borrowerImage, 'PNG', 145, imageY, 120, 40);
            doc.text('Staff Signature', 14, imageY + 45);
            doc.text('Borrower Signature', 145, imageY + 45);
            const filenamePrefix = activeSignatureMode === 'borrow' ? 'peminjaman' : 'pengembalian';
            doc.save(filenamePrefix + '_' + Date.now() + '.pdf');
        }

        window.addEventListener('load', () => {
            initializeSignatures();
        });
    </script>
</x-app-layout>
