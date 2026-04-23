<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Researcher Registration - BatStateU</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-red': '#D32F2F',
                        'brand-blue': '#4a72ff',
                    }
                }
            }
        }
    </script>

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

        .page-wrapper {
            position: relative;
            z-index: 2;
            min-height: calc(100vh - 64px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .auth-card {
            display: flex;
            width: 100%;
            max-width: 980px;
            min-height: 560px;
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
            flex: 1.35;
            background: rgba(7, 15, 36, 0.72);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2.5rem 2.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.82rem 1rem;
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

        .file-input {
            width: 100%;
            padding: 0.75rem 0.9rem;
            background: rgba(255,255,255,0.96);
            border: none;
            border-bottom: 2px solid transparent;
            outline: none;
            font-size: 0.84rem;
            color: #1a2340;
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        .file-input:focus {
            background: #fff;
            border-bottom-color: #4a72ff;
            box-shadow: 0 0 0 2px rgba(74, 114, 255, 0.14);
        }

        .field-label {
            display: block;
            font-size: 0.76rem;
            font-weight: 600;
            color: rgba(255,255,255,0.92);
            margin-bottom: 0.45rem;
            letter-spacing: 0.02em;
        }

        .submit-btn {
            min-width: 160px;
            height: 46px;
            background: #4a72ff;
            color: white;
            border: none;
            font-size: 0.82rem;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            letter-spacing: 0.08em;
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            box-shadow: 0 8px 24px rgba(74, 114, 255, 0.28);
        }

        .submit-btn:hover {
            background: #3561f5;
            transform: translateY(-1px);
        }

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

        #mobile-menu {
            transition: max-height 0.3s ease, opacity 0.3s ease;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
        #mobile-menu.open {
            max-height: 300px;
            opacity: 1;
        }

        #profile_image::file-selector-button {
            padding: 0.82rem 1rem;
            border: none;
            margin-right: 12px;
            background: rgba(255,255,255,0.96);
            border-right: 1px solid rgba(26,35,64,0.08);
            color: #66758a;
            font-family: 'Inter', sans-serif;
            font-size: 0.84rem;
            font-weight: 600;
            cursor: pointer;
        }

        #profile_image:hover::file-selector-button {
            background: #ffffff;
        }

        @media (max-width: 640px) {
            .auth-card {
                flex-direction: column;
                min-height: auto;
            }
            .left-panel {
                padding: 2rem 1.5rem;
                min-height: 180px;
            }
            .right-panel {
                padding: 2rem 1.5rem;
            }
            .divider {
                display: none;
            }
            body {
                overflow: auto;
            }
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
                <a href="{{ route('login.form') }}" class="bg-[#1f377a] text-white px-5 py-2 rounded-sm hover:bg-blue-800 transition shadow-sm">
                    Login
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
                <a href="{{ route('login.form') }}" class="mt-1 bg-[#1f377a] text-white text-center px-5 py-2.5 rounded hover:bg-blue-800 transition shadow-sm">
                    Login
                </a>
            </div>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="auth-card">

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
                        Create your researcher account to access the Ethics Review Committee system.
                    </p>

                    <a href="{{ route('login.form') }}"
                       style="margin-top:2.5rem; font-size:0.76rem; color:rgba(255,255,255,0.88); letter-spacing:0.1em; text-transform:uppercase; text-decoration:none; border-bottom:1px solid rgba(255,255,255,0.5); padding-bottom:2px; transition:color 0.2s;">
                        Back to Login
                    </a>
                </div>
            </div>

            <div class="divider"></div>

            <div class="right-panel">
                <h2 style="font-family:'Montserrat',sans-serif; font-weight:700; font-size:1.1rem; color:#ffffff; letter-spacing:0.08em; text-transform:uppercase; margin-bottom:0.35rem;">
                    Researcher Registration
                </h2>
                <p style="font-size:0.8rem; color:rgba(255,255,255,0.82); margin-bottom:2rem; font-weight:400; letter-spacing:0.03em;">
                    Fill out the form below to create your account
                </p>

                @if($errors->any())
                    <div style="font-size:0.78rem; color:#ffe4e6; margin-bottom:1rem; background:rgba(248,113,113,0.16); padding:0.75rem 0.9rem; border-left:3px solid #f87171; font-weight:500;">
                        <ul style="margin:0; padding-left:1rem;">
                            @foreach($errors->all() as $error)
                                <li style="margin-bottom:0.2rem;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('signup.submit') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="space-y-4">
                    @csrf

                    <div>
                        <label class="field-label">Full Name</label>
                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               class="form-input"
                               placeholder="Dr. Juan R. Dela Cruz">
                    </div>

                    <div>
                        <label class="field-label">Email Address</label>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               class="form-input"
                               placeholder="Enter your email address">
                    </div>

                    <div>
                        <label class="field-label">Password</label>
                        <input type="password"
                               name="password"
                               required
                               class="form-input"
                               placeholder="Create a password">
                    </div>

                    <div>
                        <label class="field-label">Confirm Password</label>
                        <input type="password"
                               name="password_confirmation"
                               required
                               class="form-input"
                               placeholder="Confirm your password">
                    </div>

                    <div>
                        <label class="field-label">Profile Image (Optional)</label>
                        <input type="file"
                            name="profile_image"
                            id="profile_image"
                            class="file-input"
                            style="padding: 0; color: #1a2340; background: rgba(255,255,255,0.96); border-bottom: 2px solid transparent;">
                    </div>

                    <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; padding-top:0.5rem; flex-wrap:wrap;">
                        <div style="font-size:0.78rem; color:rgba(255,255,255,0.82);">
                            Already have an account?
                            <a href="{{ route('login.form') }}"
                               style="color:#ff9b9b; text-decoration:none; font-weight:600;">Login</a>
                        </div>

                        <button type="submit"
                                onclick="return confirm('Account details look good? Click OK to proceed.')"
                                class="submit-btn">
                            Create Account
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
