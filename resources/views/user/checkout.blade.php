<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FurniStock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .step-active { @apply bg-amber-500 text-white; }
        .step-inactive { @apply bg-slate-100 text-slate-500; }
        .step-completed { @apply bg-green-100 text-green-600; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <!-- Navigation -->
    <nav class="bg-white border-b border-slate-100 py-6 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            <a href="{{ route('cart.index') }}" class="flex items-center gap-2 group">
                <i data-lucide="arrow-left" class="w-5 h-5 text-slate-400 group-hover:text-amber-600 transition-colors"></i>
                <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900">Kembali ke Keranjang</span>
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
        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-center">
                <div class="flex items-center">
                    <div class="step-completed rounded-full w-8 h-8 flex items-center justify-center">
                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                    </div>
                    <div class="w-16 h-0.5 bg-green-100"></div>
                    
                    <div class="step-active rounded-full w-8 h-8 flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                    </div>
                    <div class="w-16 h-0.5 bg-slate-100"></div>
                    
                    <div class="step-inactive rounded-full w-8 h-8 flex items-center justify-center">
                        <i data-lucide="package" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>
            <div class="flex justify-center mt-2 text-sm">
                <div class="text-center w-32">
                    <div class="font-bold text-green-600">Keranjang</div>
                </div>
                <div class="text-center w-32">
                    <div class="font-bold text-amber-600">Checkout</div>
                </div>
                <div class="text-center w-32">
                    <div class="font-bold text-slate-500">Selesai</div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                    @csrf
                    
                    <!-- Customer Information -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm mb-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-6">Informasi Penerima</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap *</label>
                                <input type="text" 
                                       name="customer_name" 
                                       value="{{ Auth::user()->name }}"
                                       required
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nomor Telepon *</label>
                                <input type="tel" 
                                       name="customer_phone" 
                                       value="{{ Auth::user()->phone }}"
                                       required
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Alamat Pengiriman *</label>
                                <textarea name="shipping_address" 
                                          rows="3"
                                          required
                                          class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">{{ Auth::user()->address }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Pesanan (Opsional)</label>
                                <textarea name="notes" 
                                          rows="2"
                                          placeholder="Contoh: Tandai paket dengan 'Hati-hati fragile'"
                                          class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm mb-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-6">Metode Pembayaran</h2>
                        
                        <div class="space-y-4">
                            <!-- Bank Transfer -->
                            <div class="border border-slate-200 rounded-xl p-4 hover:border-amber-500 transition-all">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" 
                                           name="payment_method" 
                                           value="bank_transfer" 
                                           class="w-4 h-4 text-amber-600 focus:ring-amber-500"
                                           id="bankTransfer"
                                           checked>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="font-bold text-slate-800">Transfer Bank</span>
                                                <p class="text-sm text-slate-500 mt-1">Transfer ke rekening bank kami</p>
                                            </div>
                                            <div class="flex gap-2">
                                                <div class="px-3 py-1 bg-blue-100 text-blue-600 text-xs font-bold rounded-full">BCA</div>
                                                <div class="px-3 py-1 bg-green-100 text-green-600 text-xs font-bold rounded-full">BRI</div>
                                                <div class="px-3 py-1 bg-red-100 text-red-600 text-xs font-bold rounded-full">Mandiri</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Bank Selection (shown when selected) -->
                                        <div id="bankSelection" class="mt-4 p-4 bg-slate-50 rounded-lg">
                                            <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Bank</label>
                                            <select name="bank_name" 
                                                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                                <option value="BCA">BCA - 7145083982 (PT FurniStock Indonesia)</option>
                                                <option value="BRI">BRI - 0987654321 (PT FurniStock Indonesia)</option>
                                                <option value="Mandiri">Mandiri - 5678901234 (PT FurniStock Indonesia)</option>
                                                <option value="BNI">BNI - 4321098765 (PT FurniStock Indonesia)</option>
                                            </select>
                                            <p class="text-sm text-slate-500 mt-2">
                                                <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                                                Pembayaran akan diverifikasi dalam 1x24 jam setelah transfer
                                            </p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- E-Wallet -->
                            <div class="border border-slate-200 rounded-xl p-4 hover:border-amber-500 transition-all">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" 
                                           name="payment_method" 
                                           value="e_wallet" 
                                           class="w-4 h-4 text-amber-600 focus:ring-amber-500"
                                           id="eWallet">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="font-bold text-slate-800">E-Wallet</span>
                                                <p class="text-sm text-slate-500 mt-1">Bayar dengan dompet digital</p>
                                            </div>
                                            <div class="flex gap-2">
                                                <div class="px-3 py-1 bg-purple-100 text-purple-600 text-xs font-bold rounded-full">OVO</div>
                                                <div class="px-3 py-1 bg-green-100 text-green-600 text-xs font-bold rounded-full">DANA</div>
                                                <div class="px-3 py-1 bg-blue-100 text-blue-600 text-xs font-bold rounded-full">GOPAY</div>
                                            </div>
                                        </div>
                                        
                                        <!-- E-Wallet Selection (shown when selected) -->
                                        <div id="walletSelection" class="mt-4 p-4 bg-slate-50 rounded-lg hidden">
                                            <label class="block text-sm font-medium text-slate-700 mb-2">Pilih E-Wallet</label>
                                            <select name="wallet_type" 
                                                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                                <option value="OVO">OVO - 089676737200</option>
                                                <option value="DANA">DANA - 089676737200</option>
                                                <option value="GOPAY">GoPay - 089676737200</option>
                                                <option value="ShopeePay">ShopeePay - 089676737200</option>
                                            </select>
                                            <p class="text-sm text-slate-500 mt-2">
                                                <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                                                Pembayaran instan, pesanan diproses lebih cepat
                                            </p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Payment Terms -->
                        <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                            <div class="flex items-start gap-2">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-amber-600 mt-0.5"></i>
                                <div>
                                    <h4 class="font-bold text-amber-800">Ketentuan Pembayaran</h4>
                                    <ul class="text-sm text-amber-700 mt-2 space-y-1">
                                        <li>• Batas waktu pembayaran: 24 jam setelah pesanan dibuat</li>
                                        <li>• Upload bukti transfer untuk metode Bank Transfer</li>
                                        <li>• Pesanan akan diproses setelah pembayaran dikonfirmasi</li>
                                        <li>• Pembatalan otomatis jika tidak dibayar dalam 24 jam</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 lg:p-8 rounded-2xl border border-slate-100 shadow-sm sticky top-32">
                    <h2 class="text-xl font-bold text-slate-800 mb-6">Ringkasan Pesanan</h2>
                    
                    <!-- Cart Items Preview -->
                    <div class="space-y-3 mb-6 max-h-64 overflow-y-auto pr-2">
                        @foreach($cartItems as $item)
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-100 last:border-0">
                            <div class="w-16 h-16 bg-slate-100 rounded-lg overflow-hidden shrink-0">
                                @php
                                    $product = $item->furniture;
                                    $images = $product->images;
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
                                     class="w-full h-full object-cover"
                                     onerror="this.src='https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?w=400'">
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-sm text-slate-800">{{ $product->name }}</h4>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-slate-500 text-sm">x{{ $item->quantity }}</span>
                                    <span class="font-bold text-slate-800 text-sm">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Order Details -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-slate-600">Subtotal ({{ count($cartItems) }} item)</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Ongkos Kirim</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($shipping, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Pajak (PPN 11%)</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <!-- Divider -->
                    <div class="border-t border-slate-100 pt-6 mb-6">
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-sm font-bold text-slate-600">Total Bayar</p>
                                <p class="text-3xl font-black text-slate-900 leading-none mt-1">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-slate-400 mt-1">Sudah termasuk semua biaya</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Agreement -->
                    <div class="mb-6">
                        <label class="flex items-start gap-2">
                            <input type="checkbox" 
                                   required
                                   class="w-4 h-4 text-amber-600 rounded focus:ring-amber-500 mt-1">
                            <span class="text-sm text-slate-600">
                                Saya menyetujui 
                                <a href="#" class="text-amber-600 hover:text-amber-700 font-bold">Syarat & Ketentuan</a> 
                                dan 
                                <a href="#" class="text-amber-600 hover:text-amber-700 font-bold">Kebijakan Privasi</a>
                                FurniStock
                            </span>
                        </label>
                    </div>
                    
                    <!-- Action Buttons -->
                    <button type="submit" 
                            form="checkoutForm"
                            class="w-full bg-amber-500 text-slate-900 py-4 rounded-2xl font-black text-sm text-center hover:bg-amber-400 transition-all shadow-lg shadow-amber-500/20 hover:shadow-xl mb-4">
                        Buat Pesanan & Bayar
                    </button>
                    
                    <!-- Back to Cart -->
                    <a href="{{ route('cart.index') }}" 
                       class="block w-full border border-slate-200 text-slate-600 py-3 rounded-xl text-sm text-center hover:bg-slate-50 transition-all">
                        <i data-lucide="arrow-left" class="w-4 h-4 inline mr-1"></i>
                        Kembali ke Keranjang
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-sm text-slate-500">
                &copy; {{ date('Y') }} FurniStock. All rights reserved.
                <span class="block sm:inline mt-2 sm:mt-0">Jl. Perabotan No. 123, Jakarta | Telp: (021) 1234-5678</span>
            </p>
        </div>
    </footer>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Payment method toggle
        document.getElementById('bankTransfer').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('bankSelection').classList.remove('hidden');
                document.getElementById('walletSelection').classList.add('hidden');
            }
        });
        
        document.getElementById('eWallet').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('walletSelection').classList.remove('hidden');
                document.getElementById('bankSelection').classList.add('hidden');
            }
        });
        
        // Form validation
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            if (!paymentMethod) {
                e.preventDefault();
                alert('Silakan pilih metode pembayaran!');
                return false;
            }
            
            if (paymentMethod.value === 'bank_transfer') {
                const bankSelect = document.querySelector('select[name="bank_name"]');
                if (!bankSelect.value) {
                    e.preventDefault();
                    alert('Silakan pilih bank tujuan!');
                    return false;
                }
            }
            
            if (paymentMethod.value === 'e_wallet') {
                const walletSelect = document.querySelector('select[name="wallet_type"]');
                if (!walletSelect.value) {
                    e.preventDefault();
                    alert('Silakan pilih jenis e-wallet!');
                    return false;
                }
            }
            
            // Show loading
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin inline"></i> Memproses...';
            }
            
            return true;
        });
        
        // Auto format phone number
        const phoneInput = document.querySelector('input[name="customer_phone"]');
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('0')) {
                value = '62' + value.substring(1);
            }
            if (value.length > 2) {
                value = value.substring(0, 2) + '-' + value.substring(2);
            }
            if (value.length > 7) {
                value = value.substring(0, 7) + '-' + value.substring(7);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>