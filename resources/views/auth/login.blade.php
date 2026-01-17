<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurniStock - Sistem Manajemen Perabot</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            background: 
                linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.9)),
                url('https://images.unsplash.com/photo-1555041469-a586c61ea9bc?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            overflow-x: hidden;
        }

        /* Container */
        .container {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            width: 100%;
            max-width: 1000px;
            min-height: 650px;
            position: relative;
            overflow: hidden;
        }

        /* Form Containers */
        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
            width: 50%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .sign-in-container {
            left: 0;
            z-index: 2;
            opacity: 1;
        }

        .sign-up-container {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
        }

        .container.right-panel-active .sign-in-container {
            transform: translateX(100%);
            opacity: 0;
        }

        .container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: show 0.6s;
        }

        @keyframes show {
            0%, 49.99% { opacity: 0; z-index: 1; }
            50%, 100% { opacity: 1; z-index: 5; }
        }

        /* Overlay Styling - Wood/Gold Theme */
        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }

        .container.right-panel-active .overlay-container {
            transform: translateX(-100%);
        }

        .overlay {
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
            background-image: url('https://www.transparenttextures.com/patterns/wood-pattern.png');
            color: #fff;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .container.right-panel-active .overlay {
            transform: translateX(50%);
        }

        .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transition: transform 0.6s ease-in-out;
        }

        .overlay-left { transform: translateX(-20%); }
        .container.right-panel-active .overlay-left { transform: translateX(0); }
        .overlay-right { right: 0; transform: translateX(0); }
        .container.right-panel-active .overlay-right { transform: translateX(20%); }

        /* Shared Form Elements */
        form {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-title { font-size: 32px; font-weight: 800; margin-bottom: 8px; color: #f8fafc; }
        .form-subtitle { color: #94a3b8; font-size: 14px; margin-bottom: 24px; text-align: center; }

        input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 14px 16px;
            margin: 8px 0;
            width: 100%;
            border-radius: 16px;
            color: white;
            font-size: 14px;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #fbbf24;
            background: rgba(255, 255, 255, 0.1);
        }

        button {
            border-radius: 16px;
            border: none;
            background: linear-gradient(to right, #fbbf24, #d97706);
            color: #451a03;
            font-size: 14px;
            font-weight: 800;
            padding: 16px 0;
            width: 100%;
            text-transform: uppercase;
            cursor: pointer;
            margin-top: 15px;
            transition: all 0.3s;
            box-shadow: 0 10px 15px -3px rgba(251, 191, 36, 0.3);
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 20px -5px rgba(251, 191, 36, 0.4);
        }

        button.ghost {
            background: transparent;
            border: 2px solid #fbbf24;
            color: #fbbf24;
            margin-top: 20px;
            box-shadow: none;
        }

        button.ghost:hover {
            background: #fbbf24;
            color: #451a03;
        }

        .member-info-box {
            background: rgba(251, 191, 36, 0.05);
            padding: 15px;
            border-radius: 15px;
            margin-top: 20px;
            width: 100%;
            border: 1px dashed rgba(251, 191, 36, 0.2);
        }

        /* Decorative Icons */
        .decor-icon {
            position: fixed;
            pointer-events: none;
            opacity: 0.1;
            z-index: -1;
            animation: float 15s linear infinite;
        }

        @keyframes float {
            from { transform: translateY(110vh) rotate(0); }
            to { transform: translateY(-10vh) rotate(360deg); }
        }

        /* Error & Alerts */
        .error-message { color: #f87171; font-size: 12px; margin-top: 4px; display: none; width: 100%; text-align: left; }
        .error-message.show { display: block; }

        .alert {
            padding: 16px; border-radius: 16px; margin-bottom: 20px; display: none; width: 90%; max-width: 400px;
            position: fixed; top: 40px; left: 50%; transform: translateX(-50%); z-index: 1002;
            backdrop-filter: blur(10px); text-align: center; font-weight: 600;
        }
        .alert.show { display: block; }
        .alert-success { background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; color: #4ade80; }
        .alert-error { background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #f87171; }

        @media (max-width: 768px) {
            .overlay-container { display: none; }
            .form-container { width: 100%; }
            .container { min-height: 550px; }
        }
    </style>
</head>
<body>

    <!-- Logo Section -->
    <div style="position:fixed; top:40px; left:40px; display:flex; align-items:center; gap:12px; z-index:1001;">
        <div style="background: #fbbf24; padding: 10px; border-radius: 14px; box-shadow: 0 4px 10px rgba(251, 191, 36, 0.4);">
            <i data-lucide="armchair" style="width:24px; height:24px; color:#451a03;"></i>
        </div>
        <span style="font-weight:900; font-size:24px; letter-spacing: -1px;">Furni<span style="color:#fbbf24">Stock</span></span>
    </div>

    <!-- Notifications -->
    <div id="alertContainer">
        @if(session('success'))
        <div class="alert alert-success show">{{ session('success') }}</div>
        @endif
        @if($errors->any())
        <div class="alert alert-error show">{{ $errors->first() }}</div>
        @endif
    </div>

    <div class="container" id="container">
        <!-- Sign Up Form (Register) -->
        <div class="form-container sign-up-container">
            <form id="registerForm" method="POST" action="{{ route('register.post') }}">
                @csrf
                <h2 class="form-title">Buat Akun</h2>
                <p class="form-subtitle">Kelola inventaris perabot Anda dengan lebih efisien</p>

                <input type="text" name="name" placeholder="Nama Lengkap" required />
                <input type="email" name="email" placeholder="Alamat Email" required />
                <input type="password" name="password" placeholder="Kata Sandi" required />
                <input type="password" name="password_confirmation" placeholder="Konfirmasi Kata Sandi" required />

                <button type="submit">Daftar Sekarang</button>

                <div class="member-info-box">
                    <p style="font-size: 12px; text-align: center; color: #fbbf24;">Akses penuh ke dashboard analitik dan manajemen stok otomatis.</p>
                </div>
                
                <p id="mobileSignIn" style="display:none; margin-top:20px; font-size:14px; color:#94a3b8;">
                    Sudah punya akun? <span id="signInMobileBtn" style="color:#fbbf24; cursor:pointer; font-weight:700;">Masuk</span>
                </p>
            </form>
        </div>

        <!-- Sign In Form (Login) -->
        <div class="form-container sign-in-container">
            <form id="loginForm" method="POST" action="{{ route('login.post') }}">
                @csrf
                <h2 class="form-title">Selamat Datang</h2>
                <p class="form-subtitle">Masuk untuk mengelola stok perabot rumah tangga</p>

                <input type="email" name="email" placeholder="Email Terdaftar" required />
                <input type="password" name="password" placeholder="Kata Sandi" required />

                <a href="#" style="color:#94a3b8; font-size:12px; align-self:flex-end; margin-top:5px; text-decoration:none; transition: color 0.3s;" onmouseover="this.style.color='#fbbf24'" onmouseout="this.style.color='#94a3b8'">Lupa kata sandi?</a>

                <button type="submit">Masuk ke Sistem</button>

                <p id="mobileSignUp" style="display:none; margin-top:20px; font-size:14px; color:#94a3b8;">
                    Belum punya akses? <span id="signUpMobileBtn" style="color:#fbbf24; cursor:pointer; font-weight:700;">Daftar</span>
                </p>
            </form>
        </div>

        <!-- Overlay Panel -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h2 class="form-title">Sudah Terdaftar?</h2>
                    <p class="form-subtitle">Kembali masuk untuk melanjutkan pemantauan stok perabot Anda.</p>
                    <button class="ghost" id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h2 class="form-title">Belum Punya Akun?</h2>
                    <p class="form-subtitle">Daftarkan bisnis perabot Anda dan mulai digitalisasi inventaris hari ini.</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const container = document.getElementById('container');
        const signUpBtn = document.getElementById('signUp');
        const signInBtn = document.getElementById('signIn');
        const signUpMobileBtn = document.getElementById('signUpMobileBtn');
        const signInMobileBtn = document.getElementById('signInMobileBtn');

        signUpBtn.onclick = () => container.classList.add('right-panel-active');
        signInBtn.onclick = () => container.classList.remove('right-panel-active');

        // Mobile Switch Logic
        if (window.innerWidth <= 768) {
            document.getElementById('mobileSignUp').style.display = 'block';
            document.getElementById('mobileSignIn').style.display = 'block';
            signUpMobileBtn.onclick = () => container.classList.add('right-panel-active');
            signInMobileBtn.onclick = () => container.classList.remove('right-panel-active');
        }

        // Floating Furniture Icons
        

        // Auto-hide alert
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(a => a.classList.remove('show'));
        }, 4000);
    </script>
</body>
</html>