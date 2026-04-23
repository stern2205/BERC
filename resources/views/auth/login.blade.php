<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BERC Login</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .bg-full {
            position: fixed;
            inset: 0;
            background-image: url('{{ asset('images/background.jpg') }}');
            background-size: cover;
            background-position: center;
            z-index: 0;
        }

        .bg-overlay {
            position: fixed;
            inset: 0;
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            background: rgba(10, 20, 50, 0.72);
            z-index: 1;
        }

        .login-wrapper {
            position: relative;
            z-index: 2;
            min-height: calc(100vh - 64px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .login-card {
            display: flex;
            width: 100%;
            max-width: 860px;
            min-height: 480px;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
        }

        .left-panel {
            flex: 1;
            background: linear-gradient(160deg, #1a2d6b 0%, #0f1e4a 60%, #0b1530 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2.5rem;
            border-right: 1px solid rgba(255,255,255,0.08);
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('{{ asset('images/background.jpg') }}') center/cover no-repeat;
            opacity: 0.08;
        }

        .divider {
            width: 1px;
            background: rgba(255,255,255,0.18);
            align-self: stretch;
        }

        .right-panel {
            flex: 1.3;
            background: rgba(7, 15, 36, 0.72);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2.5rem 2.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(255,255,255,0.96);
            border: none;
            border-bottom: 2px solid transparent;
            outline: none;
            font-size: 0.875rem;
            color: #1a2340;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
        }

        .form-input:focus {
            background: #fff;
            border-bottom-color: #4a72ff;
            box-shadow: 0 0 0 2px rgba(74, 114, 255, 0.14);
        }

        .form-input::placeholder {
            color: #66758a;
            font-weight: 400;
        }

        .login-btn {
            width: 60px;
            height: 44px;
            background: #4a72ff;
            color: white;
            border: none;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, transform 0.15s;
            letter-spacing: 0.05em;
            font-family: 'Montserrat', sans-serif;
            flex-shrink: 0;
            box-shadow: 0 8px 24px rgba(74, 114, 255, 0.28);
        }

        .login-btn:hover {
            background: #3561f5;
            transform: translateX(2px);
        }

        .input-row {
            display: flex;
            align-items: center;
            gap: 0;
            margin-bottom: 1rem;
        }

        .input-addon {
            width: 44px;
            height: 44px;
            background: rgba(255,255,255,0.18);
            border-left: 1px solid rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .input-addon svg {
            width: 16px;
            height: 16px;
            stroke: rgba(255,255,255,0.92);
        }

        .pw-wrapper {
            position: relative;
            flex: 1;
        }

        .pw-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #66758a;
            display: flex;
            align-items: center;
            padding: 0;
        }

        .pw-toggle:hover { color: #1a2340; }

        .link-red {
            color: #ff7c7c;
            text-decoration: none;
            font-size: 0.78rem;
            font-weight: 500;
        }
        .link-red:hover { text-decoration: underline; color: #ff9b9b; }

        .footer-links {
            position: fixed;
            bottom: 1.25rem;
            right: 1.5rem;
            z-index: 10;
            display: flex;
            gap: 1rem;
            font-size: 0.7rem;
            color: rgba(255,255,255,0.72);
        }

        .footer-links a {
            color: rgba(255,255,255,0.78);
            text-decoration: none;
            transition: color 0.2s;
            font-weight: 500;
        }

        .footer-links a:hover { color: #ffffff; }

        #mobile-menu { transition: max-height 0.3s ease, opacity 0.3s ease; max-height: 0; opacity: 0; overflow: hidden; }
        #mobile-menu.open { max-height: 300px; opacity: 1; }

        @media (max-width: 640px) {
            .login-card { flex-direction: column; min-height: auto; }
            .left-panel { padding: 2rem 1.5rem; min-height: 180px; }
            .right-panel { padding: 2rem 1.5rem; }
            .divider { display: none; }
            body { overflow: auto; }
        }
    </style>
</head>

<body>

    <div class="bg-full"></div>
    <div class="bg-overlay"></div>

    <nav class="bg-[#fafBf9] border-b border-gray-100 sticky top-0 z-50 h-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-full">

            <div class="flex items-center">
                <div class="relative w-20 sm:w-24 h-16 flex items-center">
                    <img src="{{ asset('logo/BERC.png') }}" alt="BSU Logo" class="h-10 sm:h-7 w-auto object-contain shrink-0">
                </div>
                <div class="flex flex-col justify-center ml-3 sm:ml-4 border-l-2 border-gray-200 pl-3 sm:pl-5 py-1">
                    <h1 class="text-[11px] sm:text-[15px] font-bold text-[#1f377a] leading-none tracking-tight uppercase">
                        Batangas State University - TNEU
                    </h1>
                    <h2 class="text-[9px] sm:text-[10px] font-bold text-red-600 tracking-[0.18em] uppercase mt-1 sm:mt-1.5">
                        Ethics Review Committee
                    </h2>
                </div>
            </div>

            <div class="hidden md:flex items-center space-x-8 text-[11px] font-bold text-gray-500 uppercase tracking-widest">
                <a href="/" class="hover:text-blue-900 transition-colors">Home</a>
                <a href="/#facts" class="hover:text-blue-900 transition-colors">Facts</a>
                <a href="/#about" class="hover:text-blue-900 transition-colors">About</a>
                <a href="{{ route('signup.form') }}" class="bg-[#1f377a] text-white px-5 py-2 rounded-sm hover:bg-blue-800 transition shadow-sm">
                    Sign Up
                </a>
            </div>

            <button id="hamburger" class="md:hidden flex flex-col justify-center items-center gap-[5px] w-9 h-9 rounded border border-gray-200 bg-white" aria-label="Toggle menu">
                <span class="block w-5 h-[2px] bg-gray-700 rounded transition-all" id="hb1"></span>
                <span class="block w-5 h-[2px] bg-gray-700 rounded transition-all" id="hb2"></span>
                <span class="block w-5 h-[2px] bg-gray-700 rounded transition-all" id="hb3"></span>
            </button>
        </div>

        <div id="mobile-menu" class="md:hidden bg-white border-t border-gray-100 shadow-md">
            <div class="flex flex-col px-5 py-4 space-y-3 text-[12px] font-bold text-gray-600 uppercase tracking-widest">
                <a href="/" class="hover:text-blue-900 transition-colors py-1">Home</a>
                <a href="/#facts" class="hover:text-blue-900 transition-colors py-1">Facts</a>
                <a href="/#about" class="hover:text-blue-900 transition-colors py-1">About</a>
                <a href="{{ route('signup.form') }}" class="mt-1 bg-[#1f377a] text-white text-center px-5 py-2.5 rounded hover:bg-blue-800 transition shadow-sm">
                    Sign Up
                </a>
            </div>
        </div>
    </nav>

    <div class="login-wrapper">
        <div class="login-card">

            <div class="left-panel">
                <div class="relative z-10 flex flex-col items-center text-center">
                    <img src="{{ asset('logo/bsu_logo.png') }}"
                         alt="Batangas State University Logo"
                         class="w-24 h-24 rounded-full object-contain bg-white shadow-lg mb-6">

                    <h1 style="font-family:'Montserrat',sans-serif; font-weight:800; font-size:1.35rem; color:#ffffff; letter-spacing:0.05em; text-transform:uppercase; line-height:1.2; margin-bottom:0.5rem;">
                        BERC
                    </h1>
                    <p style="font-family:'Montserrat',sans-serif; font-weight:500; font-size:0.76rem; color:rgba(255,255,255,0.82); letter-spacing:0.22em; text-transform:uppercase; margin-bottom:1.75rem;">
                        Batangas State University – TNEU
                    </p>
                    <div style="width:36px; height:1px; background:rgba(255,255,255,0.42); margin-bottom:1.5rem;"></div>
                    <p style="font-size:0.8rem; color:rgba(255,255,255,0.8); font-weight:400; max-width:220px; line-height:1.8; letter-spacing:0.02em;">
                        Ethics Review Committee Online System
                    </p>

                    <a href="{{ route('signup.form') }}"
                       style="margin-top:2.5rem; font-size:0.76rem; color:rgba(255,255,255,0.88); letter-spacing:0.1em; text-transform:uppercase; text-decoration:none; border-bottom:1px solid rgba(255,255,255,0.5); padding-bottom:2px; transition:color 0.2s;">
                        Create New Account
                    </a>
                </div>
            </div>

            <div class="divider"></div>

            <div class="right-panel">
                <h2 style="font-family:'Montserrat',sans-serif; font-weight:700; font-size:1.1rem; color:#ffffff; letter-spacing:0.08em; text-transform:uppercase; margin-bottom:0.35rem;">
                    Sign In
                </h2>
                <p style="font-size:0.8rem; color:rgba(255,255,255,0.82); margin-bottom:2rem; font-weight:400; letter-spacing:0.03em;">
                    Access your BERC account
                </p>

                @if(session('success'))
                    <p style="font-size:0.78rem; color:#dcfce7; margin-bottom:1rem; background:rgba(34,197,94,0.16); padding:0.65rem 0.8rem; border-left:3px solid #4ade80; font-weight:500;">
                        {{ session('success') }}
                    </p>
                @endif

                @if($errors->any())
                    <p style="font-size:0.78rem; color:#ffe4e6; margin-bottom:1rem; background:rgba(248,113,113,0.16); padding:0.65rem 0.8rem; border-left:3px solid #f87171; font-weight:500;">
                        {{ $errors->first() }}
                    </p>
                @endif

                <form action="{{ route('login.submit') }}" method="POST">
                    @csrf

                    <div class="input-row">
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="Email"
                               class="form-input"
                               style="padding-right:2.5rem;"
                               required>
                        <span class="input-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </span>
                    </div>

                    <div class="input-row" style="margin-bottom:0.5rem;">
                        <div class="pw-wrapper">
                            <input type="password"
                                   name="password"
                                   id="passwordInput"
                                   placeholder="Password"
                                   class="form-input"
                                   style="padding-right:2.5rem;"
                                   required>
                            <button type="button" class="pw-toggle" onclick="togglePassword()">
                                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:16px;height:16px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:16px;height:16px;display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <span class="input-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </span>
                    </div>

                    <div style="display:flex; align-items:center; justify-content:flex-end; margin-bottom:1.75rem;">
                        <a href="{{ route('forget.form') }}" class="link-red">Forget Password?</a>
                    </div>

                    <div style="display:flex; align-items:center; justify-content:flex-end; gap:1rem;">
                        <span style="font-size:0.76rem; color:rgba(255,255,255,0.8); letter-spacing:0.08em; text-transform:uppercase; font-weight:500;">Go</span>
                        <button type="submit" class="login-btn" aria-label="Login">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:18px;height:18px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    <div class="footer-links">
        <a href="#">About us</a>
        <span style="color:rgba(255,255,255,0.45);">|</span>
        <a href="#">Contact</a>
        <span style="color:rgba(255,255,255,0.45);">|</span>
        <a href="#">Help</a>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("passwordInput");
            var eyeOpen = document.getElementById("eyeOpen");
            var eyeClosed = document.getElementById("eyeClosed");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeOpen.style.display = "none";
                eyeClosed.style.display = "block";
            } else {
                passwordField.type = "password";
                eyeOpen.style.display = "block";
                eyeClosed.style.display = "none";
            }
        }

        var hamburger = document.getElementById('hamburger');
        var mobileMenu = document.getElementById('mobile-menu');
        var hb1 = document.getElementById('hb1');
        var hb2 = document.getElementById('hb2');
        var hb3 = document.getElementById('hb3');
        var menuOpen = false;

        hamburger.addEventListener('click', function () {
            menuOpen = !menuOpen;
            mobileMenu.classList.toggle('open', menuOpen);
            hb1.style.transform = menuOpen ? 'translateY(7px) rotate(45deg)' : '';
            hb2.style.opacity   = menuOpen ? '0' : '1';
            hb3.style.transform = menuOpen ? 'translateY(-7px) rotate(-45deg)' : '';
        });
    </script>

</body>
</html>
