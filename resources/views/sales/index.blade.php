<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Daftar Penjualan</h2>
                <a href="{{ route('sales.create') }}"
                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-gray font-medium rounded-lg shadow">
                    + Tambah Penjualan
                </a>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                <table class="w-full border-collapse text-sm text-left text-gray-700">
                    <thead class="bg-gray-800 text-white uppercase text-xs">
                        <tr>
                            <th class="px-5 py-3">No</th>
                            <th class="px-5 py-3">Invoice</th>
                            <th class="px-5 py-3">Sales</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Jumlah Produk</th>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
@forelse ($sales as $sale)

    {{-- ROW UTAMA --}}
    <tr class="hover:bg-gray-50 transition">
        <td class="px-5 py-3 text-gray-600">{{ $loop->iteration }}</td>
        <td class="px-5 py-3 font-semibold text-gray-800">
            {{ $sale->invoice_number }}
        </td>
        <td class="px-5 py-3">{{ Auth::user()->name ?? 'Administrator' }}</td>
        <td class="px-5 py-3">
            @php
                $users = $sale->details
                    ->flatMap(fn($d) => $d->produk->stockLogs
                        ->pluck('user.name')
                        ->filter())
                    ->unique();
            @endphp
            {{ $users->implode(', ') ?: '-' }}
        </td>
        <td class="px-5 py-3">{{ $sale->details->count() }} item</td>
        <td class="px-5 py-3">
            {{ $sale->created_at->translatedFormat('d M Y H:i') }}
        </td>
        <td class="px-5 py-3 text-center space-x-2">
            <a href="{{ route('sales.edit', $sale->id) }}" class="text-indigo-600">Edit</a>

            <form action="{{ route('sales.destroy', $sale->id) }}" method="POST"
                class="inline"
                onsubmit="return confirm('Yakin hapus?')">
                @csrf
                @method('DELETE')
                <button class="text-red-600">Hapus</button>
            </form>

            <button onclick="toggleDetail({{ $sale->id }})"
                class="text-gray-700">
                Detail
            </button>
        </td>
    </tr>

    {{-- DETAIL --}}
    <tr id="detail-{{ $sale->id }}" class="hidden bg-gray-50">
        <td colspan="7" class="p-6 space-y-6">

            {{-- DETAIL PRODUK --}}
            <div class="bg-white border rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2">Produk</th>
                            <th class="border px-3 py-2 text-center">Harga</th>
                            <th class="border px-3 py-2 text-center">Order</th>
                            <th class="border px-3 py-2 text-center">Delivery</th>
                            <th class="border px-3 py-2 text-center">Sold</th>
                            <th class="border px-3 py-2 text-center">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->details as $detail)
                            <tr>
                                <td class="border px-3 py-2">{{ $detail->produk->nama_produk }}</td>
                                <td class="border px-3 py-2 text-center">
                                    Rp {{ number_format($detail->price,0,',','.') }}
                                </td>
                                <td class="border px-3 py-2 text-center">{{ $detail->quantity_order }}</td>
                                <td class="border px-3 py-2 text-center">{{ $detail->quantity_delivery }}</td>
                                <td class="border px-3 py-2 text-center">{{ $detail->quantity_sold }}</td>
                                <td class="border px-3 py-2 text-center font-semibold">
                                    Rp {{ number_format($detail->subtotal,0,',','.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PEMBAYARAN --}}
<div class="bg-white border rounded-xl p-6">
@php
    $totalQtySold = $sale->details->sum('quantity_sold');
@endphp

<div class="bg-white border rounded-xl p-6">
@if ($errors->any())
    <div class="mb-3 text-sm text-red-600">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <form action="{{ route('sales.payment', $sale->id) }}"
        method="POST"
        enctype="multipart/form-data"
        class="flex flex-wrap items-center justify-end gap-6">
        @csrf

        {{-- TITLE --}}
        <h4 class="text-sm font-semibold text-gray-800 mr-4">
            💳 Sistem Pembayaran
        </h4>

        {{-- STATUS --}}
        @if ($sale->payment_method)
            <span class="px-3 py-1 text-xs rounded-full
                {{ $sale->payment_method === 'cash'
                    ? 'bg-green-100 text-green-700'
                    : 'bg-blue-100 text-blue-700' }}">
                {{ strtoupper($sale->payment_method) }}
            </span>
        @else
            <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                BELUM DIBAYAR
            </span>
        @endif

        {{-- METODE --}}
        <div class="flex items-center gap-4 ml-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio"
                    name="payment_method"
                    value="cash"
                    {{ $sale->payment_method === 'cash' ? 'checked' : '' }}
                    onclick="toggleProof(null, {{ $sale->id }})">
                <span class="text-sm">Cash</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio"
                    name="payment_method"
                    value="transfer"
                    {{ $sale->payment_method === 'transfer' ? 'checked' : '' }}
                    onclick="toggleProof('transfer', {{ $sale->id }})">
                <span class="text-sm">Transfer</span>
            </label>
        </div>

        {{-- BUKTI TRANSFER --}}
        <div id="proof-{{ $sale->id }}"
            class="{{ $sale->payment_method === 'transfer' ? 'flex' : 'hidden' }} items-center gap-2">
            <input type="file"
                name="payment_proof"
                accept="image/*"
                class="text-sm border border-gray-300 rounded px-3 py-1.5"
                {{ $sale->payment_method === 'transfer' ? 'required' : '' }}>
        </div>

        {{-- BUTTON --}}
        <button
            {{ $totalQtySold < 1 ? 'disabled' : '' }}
            title="{{ $totalQtySold < 1 ? 'Qty Sold harus minimal 1' : '' }}"
            class="px-5 py-2 rounded-lg shadow whitespace-nowrap
                {{ $totalQtySold < 1
                    ? 'bg-gray-400 cursor-not-allowed text-gray-700'
                    : 'bg-indigo-600 hover:bg-indigo-700 text-black' }}">
            Kirim
        </button>
    </form>

    {{-- UX MESSAGE --}}
    @if ($totalQtySold < 1)
        <p class="mt-3 text-xs text-red-600 text-right">
            ⚠ Tidak bisa melakukan pembayaran karena belum ada produk terjual
        </p>
    @endif
</div>


        </td>
    </tr>

@empty
    <tr>
        <td colspan="7" class="text-center py-6 text-gray-500">
            Belum ada data penjualan
        </td>
    </tr>
@endforelse
</tbody>


            <div class="mt-6">
                {{ $sales->links() }}
            </div>

        </div>
    </div>

    <script>
        function toggleDetail(id) {
            document.getElementById('detail-' + id).classList.toggle('hidden');
        }

        function toggleProof(method, saleId) {
            const proof = document.getElementById('proof-' + saleId);
            method === 'transfer'
                ? proof.classList.remove('hidden')
                : proof.classList.add('hidden');
        }
    </script>

    
</x-app-layout>
