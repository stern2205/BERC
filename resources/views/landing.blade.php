<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BERC | Batangas State University - TNEU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-overlay { background: linear-gradient(to right, rgba(0,0,0,0.8) 30%, transparent 100%); }
        .blue-gradient-bg { background: linear-gradient(135deg, #1f377a 0%, #152756 100%); }

        @keyframes fade-up-first {
            0%         { opacity: 0; transform: translateY(30px); }
            8%, 42%    { opacity: 1; transform: translateY(0); }
            52%, 100%  { opacity: 0; transform: translateY(-30px); }
        }
        @keyframes fade-up-second {
            0%, 48%    { opacity: 0; transform: translateY(30px); }
            58%, 92%   { opacity: 1; transform: translateY(0); }
            100%       { opacity: 0; transform: translateY(-30px); }
        }

        .slide-first  { animation: fade-up-first  10s ease-in-out infinite; }
        .slide-second { animation: fade-up-second 10s ease-in-out infinite; }

        /* Mobile nav drawer */
        #mobile-menu { transition: max-height 0.3s ease, opacity 0.3s ease; max-height: 0; opacity: 0; overflow: hidden; }
        #mobile-menu.open { max-height: 300px; opacity: 1; }
    </style>
</head>
<body class="bg-gray-50">

    <!-- ═══════════ NAV ═══════════ -->
    <nav class="bg-[#fafBf9] border-b border-gray-100 sticky top-0 z-50 h-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-full">

            <!-- Logo + Title -->
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

            <!-- Desktop links -->
            <div class="hidden md:flex items-center space-x-8 text-[11px] font-bold text-gray-500 uppercase tracking-widest">
                <a href="#" class="hover:text-blue-900 transition-colors">Home</a>
                <a href="#" class="hover:text-blue-900 transition-colors">Facts</a>
                <a href="#" class="hover:text-blue-900 transition-colors">About</a>
                <a href="/signup" class="bg-[#1f377a] text-white px-5 py-2 rounded-sm hover:bg-blue-800 transition shadow-sm">
                    Sign Up
                </a>
            </div>

            <!-- Mobile hamburger -->
            <button id="hamburger" class="md:hidden flex flex-col justify-center items-center gap-[5px] w-9 h-9 rounded border border-gray-200 bg-white" aria-label="Toggle menu">
                <span class="block w-5 h-[2px] bg-gray-700 rounded transition-all" id="hb1"></span>
                <span class="block w-5 h-[2px] bg-gray-700 rounded transition-all" id="hb2"></span>
                <span class="block w-5 h-[2px] bg-gray-700 rounded transition-all" id="hb3"></span>
            </button>
        </div>

        <!-- Mobile menu dropdown -->
        <div id="mobile-menu" class="md:hidden bg-white border-t border-gray-100 shadow-md">
            <div class="flex flex-col px-5 py-4 space-y-3 text-[12px] font-bold text-gray-600 uppercase tracking-widest">
                <a href="#" class="hover:text-blue-900 transition-colors py-1">Home</a>
                <a href="#" class="hover:text-blue-900 transition-colors py-1">Facts</a>
                <a href="#" class="hover:text-blue-900 transition-colors py-1">About</a>
                <a href="/signup" class="mt-1 bg-[#1f377a] text-white text-center px-5 py-2.5 rounded hover:bg-blue-800 transition shadow-sm">
                    Sign Up
                </a>
            </div>
        </div>
    </nav>

    <!-- ═══════════ HERO ═══════════ -->
    <header class="relative h-[480px] sm:h-[560px] lg:h-[650px] flex items-center bg-cover bg-center overflow-hidden"
            style="background-image: url('{{ asset('images/background.jpg') }}');">
        <div class="absolute inset-0 hero-overlay"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full h-full flex items-center">
            <div class="flex flex-col w-full max-w-3xl">

                <!-- Animated text area -->
                <div class="relative h-[220px] sm:h-[240px] lg:h-[260px]">

                    <!-- Slide 1 -->
                    <div class="slide-first absolute inset-0 flex flex-col justify-center text-white">
                        <h1 class="text-3xl sm:text-4xl lg:text-6xl font-bold leading-[1.15] mb-4 sm:mb-5">
                            Streamline Your Research<br class="hidden sm:block">Ethics Review Process
                        </h1>
                        <p class="text-sm sm:text-base lg:text-lg text-gray-200 leading-relaxed max-w-xl">
                            Submit, track, and manage research applications efficiently with the BERC Online System.
                        </p>
                    </div>

                    <!-- Slide 2 -->
                    <div class="slide-second absolute inset-0 flex flex-col justify-center text-white">
                        <h1 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold leading-[1.15] mb-2 sm:mb-3 tracking-tight">
                            BATANGAS STATE UNIVERSITY<br>– TNEU
                        </h1>
                        <h2 class="text-base sm:text-lg lg:text-xl font-semibold text-red-400 tracking-[0.18em] uppercase mb-3 sm:mb-5">
                            Ethics Review Committee
                        </h2>
                        <p class="text-sm sm:text-base lg:text-lg text-gray-300 leading-relaxed max-w-xl">
                            Upholding integrity and global ethical standards in institutional research.
                        </p>
                    </div>

                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mt-8 sm:mt-10">
                    <a href="/login"
                       class="bg-[#2B4581] hover:bg-blue-700 text-white px-6 sm:px-8 py-3 rounded font-semibold transition shadow-xl text-center text-sm sm:text-base">
                        Submit Application
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- ═══════════ WHAT IS BERC ═══════════ -->
    <section class="py-16 sm:py-20 lg:py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-white via-blue-50 to-white"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-start">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-blue-900 mb-4 sm:mb-6">What is BERC?</h2>
                    <p class="text-gray-600 leading-relaxed mb-6 sm:mb-8 text-sm sm:text-base">
                        The Batangas State University Ethics Review Committee (BERC) ensures that <strong>all research</strong> involving human participants follows ethical standards and institutional guidelines.
                    </p>
                    <ul class="space-y-3 sm:space-y-4 text-gray-700 font-medium text-sm sm:text-base">
                        <li class="flex items-center">
                            <span class="w-5 h-5 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-3 text-xs shrink-0">✓</span>
                            Ethical Review
                        </li>
                        <li class="flex items-center">
                            <span class="w-5 h-5 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-3 text-xs shrink-0">✓</span>
                            Research Compliance
                        </li>
                        <li class="flex items-center">
                            <span class="w-5 h-5 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-3 text-xs shrink-0">✓</span>
                            Faculty &amp; Student Support
                        </li>
                    </ul>
                </div>

                <!-- Stats grid -->
                <div class="grid grid-cols-3 gap-3 sm:gap-4 mt-8 lg:mt-0">
                    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-xl text-center border-t-4 border-blue-600">
                        <div class="text-blue-600 mb-1 sm:mb-2 font-bold text-2xl sm:text-3xl">297</div>
                        <p class="text-[9px] sm:text-[10px] uppercase tracking-tighter text-gray-400 font-bold">Projects Completed</p>
                    </div>
                    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-xl text-center border-t-4 border-blue-600">
                        <div class="text-blue-600 mb-1 sm:mb-2 font-bold text-2xl sm:text-3xl">92%</div>
                        <p class="text-[9px] sm:text-[10px] uppercase tracking-tighter text-gray-400 font-bold">Approval Rate</p>
                    </div>
                    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-xl text-center border-t-4 border-blue-600">
                        <div class="text-blue-600 mb-1 sm:mb-2 font-bold text-2xl sm:text-3xl">460</div>
                        <p class="text-[9px] sm:text-[10px] uppercase tracking-tighter text-gray-400 font-bold">Publications</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ BERC FACTS ═══════════ -->
    <section class="py-16 sm:py-20 bg-gray-50 border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-blue-900 mb-3 sm:mb-4">BERC Facts</h2>
            <p class="text-gray-500 mb-10 sm:mb-16 italic text-sm sm:text-base">What distinguishes BERC efficiency through ethics review process:</p>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                <div class="bg-white p-5 sm:p-8 rounded-lg shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-600 rounded-lg mx-auto mb-4 sm:mb-6 flex items-center justify-center text-white">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1 sm:mb-2 text-sm sm:text-base">Create Account</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Sign up to create your personal account on the BERC Online System.</p>
                </div>
                <div class="bg-white p-5 sm:p-8 rounded-lg shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-500 rounded-lg mx-auto mb-4 sm:mb-6 flex items-center justify-center text-white">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1 sm:mb-2 text-sm sm:text-base">Submit Documents</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Fill out and upload the necessary research proposal documents.</p>
                </div>
                <div class="bg-white p-5 sm:p-8 rounded-lg shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-sky-500 rounded-lg mx-auto mb-4 sm:mb-6 flex items-center justify-center text-white">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1 sm:mb-2 text-sm sm:text-base">Ethics Review</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">BERC conducts a thorough ethical review of your research proposal.</p>
                </div>
                <div class="bg-white p-5 sm:p-8 rounded-lg shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-500 rounded-lg mx-auto mb-4 sm:mb-6 flex items-center justify-center text-white">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1 sm:mb-2 text-sm sm:text-base">Receive Approval</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Receive feedback or approval and track your status online.</p>
                </div>
            </div>

            <a href="#" class="mt-10 sm:mt-12 inline-block text-blue-600 font-semibold text-sm hover:underline">View Guidelines →</a>
        </div>
    </section>

    <!-- ═══════════ FIRST TIME SUBMITTING ═══════════ -->
    <section class="py-16 sm:py-20 lg:py-24 blue-gradient-bg text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

                <div>
                    <h2 class="text-4xl sm:text-5xl md:text-6xl font-light tracking-tight mb-1 sm:mb-2 opacity-80">FIRST TIME</h2>
                    <h2 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight mb-6 sm:mb-8 leading-none">SUBMITTING</h2>
                    <p class="text-gray-300 mb-8 sm:mb-10 text-base sm:text-lg leading-relaxed">
                        You and your research team are welcome to submit proposals to BERC for ethics review. We are here to support you through the process.
                    </p>
                    <div class="grid grid-cols-2 gap-6 sm:gap-8">
                        <div class="border-l border-blue-400 pl-4 sm:pl-6">
                            <h4 class="font-bold mb-1 sm:mb-2 text-sm sm:text-base">GUIDELINES</h4>
                            <p class="text-xs text-gray-400">Information relevant to research ethics protocols.</p>
                        </div>
                        <div class="border-l border-blue-400 pl-4 sm:pl-6">
                            <h4 class="font-bold mb-1 sm:mb-2 text-sm sm:text-base">ORIENTATION</h4>
                            <p class="text-xs text-gray-400">Resources to suit individual researcher needs.</p>
                        </div>
                    </div>
                </div>

                <!-- Stats panel -->
                <div class="bg-blue-900/40 p-8 sm:p-12 rounded-2xl border border-blue-800 backdrop-blur-sm mt-4 lg:mt-0">
                    <div class="space-y-8 sm:space-y-12">
                        <div class="flex items-end justify-between border-b border-blue-800 pb-6">
                            <div>
                                <div class="text-4xl sm:text-5xl font-bold">89%</div>
                                <div class="text-[10px] uppercase font-bold text-blue-300 mt-1">Success Rate</div>
                            </div>
                            <p class="text-[10px] text-gray-400 max-w-[150px] text-right">Successfully completed with institutional funding.</p>
                        </div>
                        <div class="flex items-end justify-between border-b border-blue-800 pb-6">
                            <div>
                                <div class="text-4xl sm:text-5xl font-bold">92%</div>
                                <div class="text-[10px] uppercase font-bold text-blue-300 mt-1">Satisfaction</div>
                            </div>
                            <p class="text-[10px] text-gray-400 max-w-[150px] text-right">Researchers hold positions related to career goals.</p>
                        </div>
                        <div class="flex items-end justify-between">
                            <div>
                                <div class="text-4xl sm:text-5xl font-bold">217</div>
                                <div class="text-[10px] uppercase font-bold text-blue-300 mt-1">Research Proposals</div>
                            </div>
                            <p class="text-[10px] text-gray-400 max-w-[150px] text-right">Annual research projects completed in 2024.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ═══════════ FOOTER ═══════════ -->
    <footer class="bg-[#0f172a] text-gray-400 py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid sm:grid-cols-2 md:grid-cols-3 gap-10 sm:gap-12">
            <div>
                <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                    <img src="{{ asset('logo/bsu_logo.png') }}" alt="Logo" class="w-10 h-10 rounded-full object-cover opacity-80">
                    <h3 class="text-white font-bold text-base sm:text-lg">Official Platform</h3>
                </div>
                <p class="text-sm">Batangas State University<br>The National Engineering University</p>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4 sm:mb-6 text-sm uppercase tracking-widest">Quick Links</h4>
                <div class="grid grid-cols-2 gap-3 sm:gap-4 text-xs">
                    <a href="#" class="hover:text-blue-400">Submit Application</a>
                    <a href="#" class="hover:text-blue-400">Download Forms</a>
                    <a href="#" class="hover:text-blue-400">Guidelines</a>
                    <a href="#" class="hover:text-blue-400">FAQ</a>
                </div>
            </div>
            <div class="sm:col-span-2 md:col-span-1">
                <h4 class="text-white font-bold mb-4 sm:mb-6 text-sm uppercase tracking-widest">Contact</h4>
                <p class="text-xs mb-2"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="7d1f180f1e3d1f1c090e091c0918500853181908530d15">[email&#160;protected]</a></p>
                <p class="text-xs mb-2">Alangilan, Batangas City, Philippines</p>
                <p class="text-xs">(043) 425-0139 loc. 2121</p>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-gray-800 mt-10 sm:mt-12 pt-6 sm:pt-8 text-center text-[10px]">
            © {{ date('Y') }} Batangas State University – Ethics Review Committee. All rights reserved.
        </div>
    </footer>

    <script>
        // Mobile hamburger toggle
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
