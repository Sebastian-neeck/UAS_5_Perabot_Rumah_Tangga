<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - FurniStock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .invoice-print { 
            background: white; 
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .invoice-print { 
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <!-- Navigation -->
    <nav class="bg-white border-b border-slate-100 py-6 sticky top-0 z-40 no-print">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            <a href="{{ route('user.catalog') }}" class="flex items-center gap-2 group">
                <i data-lucide="arrow-left" class="w-5 h-5 text-slate-400 group-hover:text-amber-600"></i>
                <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900">Kembali ke Beranda</span>
            </a>
            <div class="flex items-center gap-2">
                <div class="bg-amber-500 p-1 rounded-lg">
                    <i data-lucide="armchair" class="w-4 h-4 text-slate-900"></i>
                </div>
                <span class="font-black tracking-tight text-slate-900">Furni<span class="text-amber-500">Stock</span></span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-slate-600">Halo, {{ Auth::user()->name }}</span>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-8">
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-200 flex items-center gap-2">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <!-- Success Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="party-popper" class="w-10 h-10 text-green-600"></i>
            </div>
            <h1 class="text-3xl font-black text-slate-900 mb-2">Pesanan Berhasil!</h1>
            <p class="text-slate-500 max-w-2xl mx-auto">
                Terima kasih telah berbelanja di FurniStock. Pesanan Anda sedang diproses. 
                Silakan lakukan pembayaran dan upload bukti transfer.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Invoice / Receipt -->
            <div class="lg:col-span-2">
                <div class="bg-white p-6 lg:p-8 rounded-2xl border border-slate-100 shadow-sm invoice-print" id="invoiceContent">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-8 pb-8 border-b border-slate-200">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <div class="bg-amber-500 p-1 rounded-lg">
                                    <i data-lucide="armchair" class="w-4 h-4 text-slate-900"></i>
                                </div>
                                <span class="font-black text-xl tracking-tight text-slate-900">Furni<span class="text-amber-500">Stock</span></span>
                            </div>
                            <p class="text-slate-500">Jl. Perabotan No. 123, Jakarta</p>
                            <p class="text-slate-500">Telp: (021) 1234-5678</p>
                            <p class="text-slate-500">Email: info@furnistock.com</p>
                        </div>
                        <div class="text-right">
                            <h2 class="text-2xl font-black text-slate-900 mb-1">INVOICE</h2>
                            <p class="text-slate-500">No: {{ $order->order_code }}</p>
                            <p class="text-slate-500">Tanggal: {{ $order->created_at->format('d/m/Y') }}</p>
                            <p class="text-slate-500">Status: 
                                <span class="font-bold {{ $order->status === 'pending' ? 'text-amber-600' : 'text-green-600' }}">
                                    {{ strtoupper($order->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Customer Info -->
                    <div class="mb-8 pb-8 border-b border-slate-200">
                        <h3 class="font-bold text-lg text-slate-800 mb-4">Informasi Pelanggan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-slate-600 text-sm">Nama</p>
                                <p class="font-bold text-slate-800">{{ $order->customer_name }}</p>
                            </div>
                            <div>
                                <p class="text-slate-600 text-sm">Telepon</p>
                                <p class="font-bold text-slate-800">{{ $order->customer_phone }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-slate-600 text-sm">Alamat Pengiriman</p>
                                <p class="font-bold text-slate-800">{{ $order->shipping_address }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="mb-8">
                        <h3 class="font-bold text-lg text-slate-800 mb-4">Detail Pesanan</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-slate-200">
                                        <th class="text-left py-3 text-slate-600 font-bold">Produk</th>
                                        <th class="text-right py-3 text-slate-600 font-bold">Harga</th>
                                        <th class="text-right py-3 text-slate-600 font-bold">Qty</th>
                                        <th class="text-right py-3 text-slate-600 font-bold">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr class="border-b border-slate-100">
                                        <td class="py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 bg-slate-100 rounded-lg overflow-hidden shrink-0">
                                                    @php
                                                        $product = $item->furniture;
                                                        $images = $product->images ?? [];
                                                        $imageUrl = null;
                                                        
                                                        if (is_array($images) && !empty($images)) {
                                                            $firstImage = $images[0];
                                                            $imageUrl = str_starts_with($firstImage, 'http') ? 
                                                                       $firstImage : 
                                                                       asset('storage/furniture/' . $firstImage);
                                                        }
                                                    @endphp
                                                    <img src="{{ $imageUrl ?? 'https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?w=400' }}" 
                                                         alt="{{ $product->name }}"
                                                         class="w-full h-full object-cover">
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-800">{{ $product->name }}</p>
                                                    <p class="text-sm text-slate-500">{{ $product->sku }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right py-4">
                                            <p class="font-bold text-slate-800">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="text-right py-4">
                                            <p class="font-bold text-slate-800">{{ $item->quantity }}</p>
                                        </td>
                                        <td class="text-right py-4">
                                            <p class="font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Summary -->
                    <div class="mb-8">
                        <div class="max-w-md ml-auto">
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Subtotal</span>
                                    <span class="font-bold text-slate-800">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Ongkos Kirim</span>
                                    <span class="font-bold text-slate-800">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Pajak (PPN 11%)</span>
                                    <span class="font-bold text-slate-800">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                                </div>
                                <div class="border-t border-slate-200 pt-2 mt-2">
                                    <div class="flex justify-between">
                                        <span class="text-lg font-bold text-slate-800">Total</span>
                                        <span class="text-2xl font-black text-slate-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Info -->
                    <div class="bg-slate-50 p-6 rounded-xl">
                        <h3 class="font-bold text-lg text-slate-800 mb-4">Informasi Pembayaran</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-slate-600 text-sm">Metode Pembayaran</p>
                                <p class="font-bold text-slate-800">
                                    {{ $order->payment_method === 'bank_transfer' ? 'Transfer Bank' : 'E-Wallet' }}
                                </p>
                                @if($order->payment_method === 'bank_transfer')
                                <p class="text-sm text-slate-500 mt-1">
                                    Transfer ke: {{ $order->bank_name ?? 'BCA' }} - 1234567890 (PT FurniStock Indonesia)
                                </p>
                                @else
                                <p class="text-sm text-slate-500 mt-1">
                                    {{ $order->wallet_type ?? 'OVO' }} - 081234567890
                                </p>
                                @endif
                            </div>
                            <div>
                                <p class="text-slate-600 text-sm">Batas Waktu Pembayaran</p>
                                <p class="font-bold text-amber-600">
                                    {{ now()->addHours(24)->format('d/m/Y H:i') }}
                                </p>
                                <p class="text-sm text-slate-500 mt-1">
                                    Upload bukti pembayaran sebelum waktu habis
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    @if($order->notes)
                    <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                        <p class="text-sm text-amber-800"><strong>Catatan:</strong> {{ $order->notes }}</p>
                    </div>
                    @endif
                    
                    <!-- Footer -->
                    <div class="mt-8 pt-8 border-t border-slate-200 text-center text-slate-500 text-sm">
                        <p>Terima kasih telah berbelanja di FurniStock!</p>
                        <p class="mt-1">Pesanan akan diproses dalam 1-2 hari kerja setelah pembayaran dikonfirmasi.</p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-6 flex flex-wrap gap-4 no-print">
                    <button onclick="downloadInvoice()" 
                            class="flex items-center gap-2 px-6 py-3 bg-amber-500 text-slate-900 font-bold rounded-xl hover:bg-amber-400 transition-all">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        Download Invoice (PDF)
                    </button>
                    <button onclick="window.print()" 
                            class="flex items-center gap-2 px-6 py-3 border border-slate-300 text-slate-700 font-bold rounded-xl hover:bg-slate-50 transition-all">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        Cetak Invoice
                    </button>
                    <a href="{{ route('user.catalog') }}" 
                       class="flex items-center gap-2 px-6 py-3 border border-slate-300 text-slate-700 font-bold rounded-xl hover:bg-slate-50 transition-all">
                        <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                        Lanjutkan Belanja
                    </a>
                </div>
            </div>
            
            <!-- Payment Instructions -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 lg:p-8 rounded-2xl border border-slate-100 shadow-sm sticky top-32">
                    <h2 class="text-xl font-bold text-slate-800 mb-6">Instruksi Pembayaran</h2>
                    
                    @if($order->payment_method === 'bank_transfer')
                    <div class="space-y-4">
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                            <div class="flex items-center gap-2 mb-2">
                                <i data-lucide="building" class="w-5 h-5 text-blue-600"></i>
                                <span class="font-bold text-blue-700">Transfer Bank</span>
                            </div>
                            <div class="space-y-2">
                                <div>
                                    <p class="text-sm text-blue-600">Bank Tujuan</p>
                                    <p class="font-bold text-blue-800">{{ $order->bank_name ?? 'BCA' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-600">Nomor Rekening</p>
                                    <p class="font-bold text-blue-800">1234567890</p>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-600">Atas Nama</p>
                                    <p class="font-bold text-blue-800">PT FurniStock Indonesia</p>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-600">Jumlah Transfer</p>
                                    <p class="font-bold text-blue-800 text-lg">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                            <h4 class="font-bold text-amber-800 mb-2">Langkah Pembayaran</h4>
                            <ol class="text-sm text-amber-700 space-y-2">
                                <li class="flex items-start gap-2">
                                    <span class="bg-amber-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mt-0.5 shrink-0">1</span>
                                    <span>Transfer ke rekening di atas sesuai total</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="bg-amber-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mt-0.5 shrink-0">2</span>
                                    <span>Simpan bukti transfer/screenshot</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="bg-amber-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mt-0.5 shrink-0">3</span>
                                    <span>Upload bukti transfer di bawah ini</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="bg-amber-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mt-0.5 shrink-0">4</span>
                                    <span>Tunggu konfirmasi dari admin (1x24 jam)</span>
                                </li>
                            </ol>
                        </div>
                    </div>
                    @else
                    <div class="space-y-4">
                        <div class="p-4 bg-purple-50 border border-purple-200 rounded-xl">
                            <div class="flex items-center gap-2 mb-2">
                                <i data-lucide="smartphone" class="w-5 h-5 text-purple-600"></i>
                                <span class="font-bold text-purple-700">E-Wallet Payment</span>
                            </div>
                            <div class="space-y-2">
                                <div>
                                    <p class="text-sm text-purple-600">E-Wallet</p>
                                    <p class="font-bold text-purple-800">{{ $order->wallet_type ?? 'OVO' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-purple-600">Nomor Tujuan</p>
                                    <p class="font-bold text-purple-800">081234567890</p>
                                </div>
                                <div>
                                    <p class="text-sm text-purple-600">Atas Nama</p>
                                    <p class="font-bold text-purple-800">FurniStore Official</p>
                                </div>
                                <div>
                                    <p class="text-sm text-purple-600">Jumlah</p>
                                    <p class="font-bold text-purple-800 text-lg">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                            <h4 class="font-bold text-amber-800 mb-2">Langkah Pembayaran</h4>
                            <ol class="text-sm text-amber-700 space-y-2">
                                <li class="flex items-start gap-2">
                                    <span class="bg-amber-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mt-0.5 shrink-0">1</span>
                                    <span>Buka aplikasi {{ $order->wallet_type ?? 'E-Wallet' }} Anda</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="bg-amber-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mt-0.5 shrink-0">2</span>
                                    <span>Pilih "Transfer" atau "Bayar"</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="bg-amber-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mt-0.5 shrink-0">3</span>
                                    <span>Masukkan nomor tujuan di atas</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="bg-amber-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mt-0.5 shrink-0">4</span>
                                    <span>Konfirmasi pembayaran dan simpan bukti</span>
                                </li>
                            </ol>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Upload Payment Proof -->
                    <div class="mt-6 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                        <h4 class="font-bold text-slate-800 mb-3">Upload Bukti Pembayaran</h4>
                        <form action="{{ route('payment.upload', $order->order_code) }}" 
                              method="POST" 
                              enctype="multipart/form-data"
                              id="uploadForm">
                            @csrf
                            <div class="mb-4">
                                <input type="file" 
                                       name="payment_proof" 
                                       id="payment_proof"
                                       accept="image/*,.pdf"
                                       required
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, PDF (max 2MB)</p>
                            </div>
                            <button type="submit" 
                                    class="w-full bg-green-600 text-white py-3 rounded-xl font-bold hover:bg-green-700 transition-all">
                                <i data-lucide="upload" class="w-4 h-4 inline mr-2"></i>
                                Upload Bukti Pembayaran
                            </button>
                        </form>
                    </div>
                    
                    <!-- Order Status -->
                    <div class="mt-6">
                        <h4 class="font-bold text-slate-800 mb-3">Status Pesanan</h4>
                        <div class="space-y-2">
                            @php
                                $steps = [
                                    'pending' => ['icon' => 'clock', 'label' => 'Menunggu Pembayaran', 'color' => 'text-amber-600'],
                                    'processing' => ['icon' => 'package', 'label' => 'Diproses', 'color' => 'text-blue-600'],
                                    'shipped' => ['icon' => 'truck', 'label' => 'Dikirim', 'color' => 'text-purple-600'],
                                    'delivered' => ['icon' => 'check-circle', 'label' => 'Selesai', 'color' => 'text-green-600'],
                                ];
                            @endphp
                            
                            @foreach($steps as $status => $info)
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full {{ $order->status === $status ? 'bg-' . str_replace('text-', '', $info['color']) . '-100' : 'bg-slate-100' }} flex items-center justify-center">
                                    <i data-lucide="{{ $info['icon'] }}" 
                                       class="w-4 h-4 {{ $order->status === $status ? $info['color'] : 'text-slate-400' }}"></i>
                                </div>
                                <div>
                                    <p class="font-bold {{ $order->status === $status ? $info['color'] : 'text-slate-400' }}">
                                        {{ $info['label'] }}
                                    </p>
                                    @if($order->status === $status)
                                    <p class="text-xs text-slate-500">Status saat ini</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100 py-8 mt-12 no-print">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-sm text-slate-500">
                &copy; {{ date('Y') }} FurniStock. All rights reserved.
                <span class="block sm:inline mt-2 sm:mt-0">Customer Service: (021) 1234-5678 (08:00-17:00)</span>
            </p>
        </div>
    </footer>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Download invoice as PDF
        function downloadInvoice() {
            const { jsPDF } = window.jspdf;
            const element = document.getElementById('invoiceContent');
            
            // Show loading
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin inline"></i> Membuat PDF...';
            button.disabled = true;
            
            html2canvas(element, {
                scale: 2,
                useCORS: true,
                logging: false,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('p', 'mm', 'a4');
                const imgWidth = 190;
                const pageHeight = 295;
                const imgHeight = canvas.height * imgWidth / canvas.width;
                let heightLeft = imgHeight;
                let position = 0;
                
                pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
                
                while (heightLeft >= 0) {
                    position = heightLeft - imgHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }
                
                pdf.save('Invoice-{{ $order->order_code }}.pdf');
                
                // Reset button
                button.innerHTML = originalText;
                button.disabled = false;
                
                // Show success message
                alert('Invoice berhasil diunduh!');
            }).catch(error => {
                console.error('Error generating PDF:', error);
                button.innerHTML = originalText;
                button.disabled = false;
                alert('Gagal mengunduh invoice. Silakan coba lagi.');
            });
        }
        
        // Handle file upload preview
        document.getElementById('payment_proof').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('File terlalu besar! Maksimal 2MB.');
                    this.value = '';
                    return;
                }
                
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
                if (!validTypes.includes(file.type)) {
                    alert('Format file tidak didukung! Hanya JPG, PNG, atau PDF.');
                    this.value = '';
                    return;
                }
            }
        });
        
        // Handle upload form submission
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('payment_proof');
            if (!fileInput.files.length) {
                e.preventDefault();
                alert('Silakan pilih file bukti pembayaran!');
                return false;
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin inline"></i> Mengupload...';
            }
            
            return true;
        });
        
        // Auto-refresh payment status every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>