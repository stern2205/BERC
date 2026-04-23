<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSU Ethics Review Committee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="{{ asset('js/functions.js') }}"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'bsu-dark': '#213C71',
                        'brand-red': '#D32F2F',
                        'light-bg': '#F8F9FA'
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Mobile nav drawer */
        #mobile-menu { transition: transform 0.3s ease, opacity 0.3s ease; }
        #mobile-menu.hidden { transform: translateY(-10px); opacity: 0; pointer-events: none; }
        #mobile-menu.open { transform: translateY(0); opacity: 1; pointer-events: auto; }

        /* Custom scrollbar for ToS */
        .tos-scroll::-webkit-scrollbar { width: 6px; }
        .tos-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .tos-scroll::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
        .tos-scroll::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }

        /* Tutorial Spotlight Layering */
        .tutorial-active { z-index: 60 !important; position: relative; background: white; }
    </style>
</head>
<body class="bg-light-bg min-h-screen">

    <div id="tutorialBackdrop" class="fixed inset-0 bg-gray-900/70 z-[55] hidden transition-opacity"></div>

    <header class="bg-white sticky top-0 z-50 shadow-sm">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 sm:h-20 flex items-center justify-between gap-3">

            <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                <img src="{{ asset('logo/BERC.png') }}" alt="BSU Logo" class="h-10 sm:h-7 w-auto object-contain shrink-0">
                <div class="flex flex-col min-w-0">
                    <h1 class="text-[11px] sm:text-[15px] font-bold text-bsu-dark leading-tight tracking-tight uppercase truncate">
                        Batangas State University - TNEU
                    </h1>
                    <p class="text-[9px] sm:text-[10px] font-bold text-brand-red leading-tight uppercase tracking-widest mt-0.5">
                        Ethics Review Committee
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="date-block">
                    <div class="time" id="clock">--:-- -- | -------</div>
                    <div class="date" id="datestamp">--/--/----</div>
                </div>

                <button id="hamburger-btn" class="md:hidden flex flex-col justify-center items-center w-9 h-9 rounded-lg border border-gray-200 gap-1.5 shrink-0" aria-label="Toggle menu">
                    <span class="block w-5 h-0.5 bg-bsu-dark rounded transition-all" id="hb1"></span>
                    <span class="block w-5 h-0.5 bg-bsu-dark rounded transition-all" id="hb2"></span>
                    <span class="block w-5 h-0.5 bg-bsu-dark rounded transition-all" id="hb3"></span>
                </button>
            </div>
        </div>

        <div class="border-t border-b border-gray-200 bg-[#FCFCFC] hidden md:block">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-gray-500">
                <div class="flex space-x-1">
                    <a href="dashboard#" class="flex items-center space-x-2 text-bsu-dark px-4 py-4">
                        <svg class="w-4 h-4 text-brand-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Back to Dashboard</span>
                    </a>
                </div>

                <div class="flex items-center space-x-6 border-l border-gray-200 pl-6 py-4">
                    <a href="{{ route('settings') }}" class="inline-block border-b-4 border-brand-red pb-1 hover:text-bsu-dark transition-colors">
                        Settings
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto p-8 max-w-7xl mx-auto w-full">

        <div class="mb-8">
            <h2 class="text-2xl font-bold theme-blue tracking-wide">SYSTEM SETTINGS</h2>
            <p class="text-gray-500 text-sm mt-1">Manage your profile and system-wide preferences</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <div class="lg:col-span-4 space-y-6">

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 flex flex-col items-center justify-center text-center">
                    <form action="{{ route('profile.update_image') }}" method="POST" enctype="multipart/form-data" class="flex flex-col items-center">
                        @csrf
                        @method('PATCH')

                        <label class="relative w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mb-2 shadow-inner overflow-hidden group cursor-pointer block">
                            <img src="{{ asset($user->profile_image ?? 'profiles/default.png') }}"
                                alt="Student Photo"
                                class="w-full h-full object-cover transition duration-300 group-hover:scale-110">

                            <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-[10px] text-white font-bold uppercase tracking-wider">Change</span>
                            </div>

                            <input type="file"
                                name="profile_image"
                                class="hidden"
                                accept="image/*"
                                onchange="this.form.submit()">
                        </label>

                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-6 lg:hidden">Tap image to change</span>
                    </form>

                    <h3 class="font-bold theme-blue text-lg uppercase tracking-wide"><?php echo htmlspecialchars($user->name); ?></h3>
                    <p class="theme-red font-semibold text-xs tracking-widest uppercase mt-1"><?php echo htmlspecialchars($user->role); ?></p>
                </div>

                <div class="bg-[#1e3a8a] rounded-xl shadow-sm text-white p-6 relative overflow-hidden">
                    <h4 class="text-xs font-bold tracking-widest text-blue-200 mb-4">SYSTEM IDENTITY</h4>
                    <div class="space-y-3 relative z-10">
                        <div class="flex items-center space-x-3 text-sm">
                            <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h3"/></svg>
                            <span class="font-medium tracking-wide"><?php echo htmlspecialchars($user->id); ?></span>
                        </div>
                        <div class="flex items-center space-x-3 text-sm">
                            <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span class="font-medium tracking-wide"><?php echo htmlspecialchars($user->email); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-6">

                <div class="bg-gray-50 rounded-xl p-8 border border-gray-100">
                    <div class="flex items-center space-x-2 mb-8">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <h3 class="text-xs font-bold text-gray-500 tracking-widest uppercase">PROFILE INFORMATION</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Full Name</label>
                            <p class="font-semibold theme-blue text-sm"><?php echo htmlspecialchars($user->name); ?></p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Email Address</label>
                            <p class="font-semibold theme-blue text-sm"><?php echo htmlspecialchars($user->email); ?></p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4 pt-2">

                    @if($user->e_signature)
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg flex flex-col items-start w-full sm:w-2/3 md:w-1/2">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Current Active Signature</span>
                            <img src="{{ route('signature.view_specific', $user->id) }}" alt="Your Digital Signature" class="max-h-20 object-contain drop-shadow-sm pointer-events-none select-none">
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center gap-4 relative">
                        <button id="openPasswordModal" type="button" class="flex items-center space-x-2 bg-white border-2 border-gray-200 text-gray-700 hover:bg-gray-50 font-bold text-xs uppercase tracking-wide py-3 px-6 rounded-lg transition shadow-sm">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span class="text-bsu-dark">Change Password</span>
                        </button>

                        <div id="signatureTutorialWrapper" class="relative rounded-lg">
                            @if($user->e_signature)
                                <button id="openRemoveSigModal" type="button" class="flex items-center space-x-2 bg-white border-2 border-red-200 text-red-600 hover:bg-red-50 font-bold text-xs uppercase tracking-wide py-3 px-6 rounded-lg transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    <span>Remove Signature</span>
                                </button>
                            @else
                                <button id="openSignatureModal" type="button" class="flex items-center space-x-2 bg-white border-2 border-gray-200 text-gray-700 hover:bg-gray-50 font-bold text-xs uppercase tracking-wide py-3 px-6 rounded-lg transition shadow-sm relative z-10 bg-white">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    <span class="text-bsu-dark">Upload Signature</span>
                                </button>
                            @endif

                            <div id="signatureTutorialTooltip" class="hidden absolute top-full left-1/2 -translate-x-1/2 mt-5 bg-bsu-dark text-white text-xs font-bold px-5 py-4 rounded-lg shadow-2xl w-72 text-center animate-bounce z-[65]">
                                <div class="absolute -top-2 left-1/2 -translate-x-1/2 w-4 h-4 bg-bsu-dark rotate-45"></div>
                                <p class="relative z-10 mb-3 leading-relaxed">Password updated successfully! Next, let's set up your digital signature to digitally sign official documents.</p>
                                <button id="dismissTutorialBtn" type="button" class="relative z-10 w-full bg-brand-red hover:bg-red-700 px-4 py-2 rounded text-[11px] uppercase tracking-widest transition shadow-md">Set Up Now</button>
                            </div>
                        </div>
                    </div>

                    <div id="passwordModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[9999] hidden flex items-center justify-center transition-opacity">
                        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
                            <div class="bg-bsu-dark px-6 py-4 flex items-center justify-between">
                                <h3 class="text-white text-[13px] font-bold uppercase tracking-widest">Change Password</h3>
                                <button id="closePasswordModal" class="text-gray-300 hover:text-white transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <form action="{{ route('password.update') }}" method="POST" id="passwordUpdateForm" class="p-6">
                                @csrf
                                @method('PUT')

                                @if ($errors->hasAny(['current_password', 'password', 'password_confirmation']))
                                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-xs font-bold">
                                        <ul>
                                            @if($errors->has('current_password'))
                                                <li>• {{ $errors->first('current_password') }}</li>
                                            @endif
                                            @if($errors->has('password'))
                                                <li>• {{ $errors->first('password') }}</li>
                                            @endif
                                            @if($errors->has('password_confirmation'))
                                                <li>• {{ $errors->first('password_confirmation') }}</li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Current Password</label>
                                        <div class="relative">
                                            <input type="password" name="current_password" required
                                                class="w-full border border-gray-300 rounded-lg pl-3 pr-10 py-2 text-sm focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red transition">
                                            <button type="button" class="toggle-password absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" class="eye-path" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" class="eye-path" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" class="eye-slash-path hidden" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">New Password</label>
                                        <div class="relative">
                                            <input type="password" name="password" required
                                                class="w-full border border-gray-300 rounded-lg pl-3 pr-10 py-2 text-sm focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red transition">
                                            <button type="button" class="toggle-password absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" class="eye-path" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" class="eye-path" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" class="eye-slash-path hidden" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Confirm New Password</label>
                                        <div class="relative">
                                            <input type="password" name="password_confirmation" required
                                                class="w-full border border-gray-300 rounded-lg pl-3 pr-10 py-2 text-sm focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red transition">
                                            <button type="button" class="toggle-password absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" class="eye-path" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" class="eye-path" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" class="eye-slash-path hidden" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end gap-3">
                                    <button type="button" id="cancelPasswordModal" class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest text-gray-500 hover:bg-gray-100 rounded-lg transition">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest bg-brand-red hover:bg-red-700 text-white rounded-lg transition shadow-md">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="signatureModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[9999] hidden flex items-center justify-center transition-opacity">
                        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all">
                            <div class="bg-bsu-dark px-6 py-4 flex items-center justify-between">
                                <h3 class="text-white text-[13px] font-bold uppercase tracking-widest">Upload Digital Signature</h3>
                                <button id="closeSignatureModalBtn" class="text-gray-300 hover:text-white transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <div class="p-6">
                                <h4 class="text-xs font-bold text-bsu-dark uppercase tracking-widest mb-3">Terms of Service & Usage Agreement</h4>
                                <div class="text-[13px] text-gray-600 space-y-4 h-48 overflow-y-auto border border-gray-200 p-4 rounded-lg bg-gray-50 tos-scroll leading-relaxed">
                                    <p>By uploading your digital signature to the BSU Ethics Review Committee (BERC) system, you acknowledge and agree to the following conditions regarding its storage, security, and application:</p>
                                    <p><strong class="text-gray-800 font-bold">1. Purpose of Use:</strong> Your electronic signature will strictly be utilized for the automated authentication of official BERC documentation. This is explicitly limited to the logging of outgoing and ingoing communications, generation of decision letters, and the completion of relevant official ISO forms required within the system workflow.</p>
                                    <p><strong class="text-gray-800 font-bold">2. Restriction of Application:</strong> Your signature will <strong>not</strong> be appended to, copied, or applied to any other personal files, generic documents, or unauthorized external communications outside the direct scope of your designated role and duties within the BERC framework.</p>
                                    <p><strong class="text-gray-800 font-bold">3. Security and Revocation:</strong> The system employs secure storage protocols to protect your signature image. You maintain the right to update, replace, or completely revoke your digital signature at any time through your profile settings. By proceeding, you grant the system authorization to electronically apply this image solely to the aforementioned official outputs triggered by your authenticated account actions.</p>
                                </div>

                                <div class="mt-5 flex items-start gap-3">
                                    <input id="agreeSignatureTos" type="checkbox" class="w-4 h-4 mt-0.5 border border-gray-300 rounded focus:ring-brand-red accent-brand-red cursor-pointer">
                                    <label for="agreeSignatureTos" class="text-xs font-bold text-gray-700 cursor-pointer select-none">
                                        I have read, understood, and agree to the Terms of Service regarding the use of my digital signature.
                                    </label>
                                </div>

                                <form action="{{ route('profile.update_signature') ?? '#' }}" method="POST" enctype="multipart/form-data" id="signatureUploadForm" class="hidden mt-6 pt-5 border-t border-gray-200">
                                    @csrf
                                    @method('PATCH')
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Select Signature Image File</label>
                                    <input type="file" name="signature_image" required accept="image/png, image/jpeg, image/jpg" class="w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-xs file:font-bold file:uppercase file:tracking-widest file:bg-bsu-dark file:text-white hover:file:bg-blue-800 transition">

                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" id="cancelSignatureModal" class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest text-gray-500 hover:bg-gray-100 rounded-lg transition">Cancel</button>
                                        <button type="submit" class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest bg-brand-red hover:bg-red-700 text-white rounded-lg transition shadow-md">Upload Signature</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div id="removeSigModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[9999] hidden flex items-center justify-center transition-opacity">
                        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden transform transition-all text-center">
                            <div class="p-8">
                                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-bsu-dark mb-2">Remove Signature?</h3>
                                <p class="text-xs text-gray-500 font-medium leading-relaxed">
                                    Are you sure you want to delete your digital signature? It will be permanently removed from the system and will no longer appear on your official decision letters or forms. You must upload a new signature to use this feature again.
                                </p>

                                <form action="{{ route('profile.remove_signature') ?? '#' }}" method="POST" class="mt-6 flex justify-center gap-3 w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" id="cancelRemoveSig" class="w-full px-4 py-2.5 text-[11px] font-bold uppercase tracking-widest text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                                    <button type="submit" class="w-full px-4 py-2.5 text-[11px] font-bold uppercase tracking-widest bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-md">Yes, Remove It</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none">
                    @if(session('success'))
                        <div class="toast-element pointer-events-auto flex items-center w-full max-w-xs p-4 space-x-3 text-gray-500 bg-white rounded-lg shadow-lg border border-gray-100 transition-all duration-500 translate-x-0 opacity-100">
                            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 bg-green-100 rounded-lg">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div class="ml-3 text-sm font-bold text-bsu-dark">{{ session('success') }}</div>
                            <button type="button" class="close-toast-btn ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"></path></svg>
                            </button>
                        </div>
                    @endif

                    @error('profile_image')
                        <div class="toast-element pointer-events-auto flex items-center w-full max-w-xs p-4 space-x-3 text-white bg-red-600 rounded-lg shadow-lg transition-all duration-500 translate-x-0 opacity-100">
                            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 bg-red-700 rounded-lg">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="ml-3 text-sm font-bold">{{ $message }}</div>
                            <button type="button" class="close-toast-btn ml-auto -mx-1.5 -my-1.5 bg-red-600 text-white hover:text-gray-200 rounded-lg p-1.5 hover:bg-red-700 inline-flex items-center justify-center h-8 w-8">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"></path></svg>
                            </button>
                        </div>
                    @enderror

                    @error('signature_image')
                        <div class="toast-element pointer-events-auto flex items-center w-full max-w-xs p-4 space-x-3 text-white bg-red-600 rounded-lg shadow-lg transition-all duration-500 translate-x-0 opacity-100">
                            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 bg-red-700 rounded-lg">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="ml-3 text-sm font-bold">{{ $message }}</div>
                            <button type="button" class="close-toast-btn ml-auto -mx-1.5 -my-1.5 bg-red-600 text-white hover:text-gray-200 rounded-lg p-1.5 hover:bg-red-700 inline-flex items-center justify-center h-8 w-8">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"></path></svg>
                            </button>
                        </div>
                    @enderror
                </div>
            </div>
        </div>
    </main>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. CORE VARIABLES ---
        const isFirstLogin = @json(auth()->user()->is_first_login ?? false);
        const passModal = document.getElementById('passwordModal');
        const passForm = document.getElementById('passwordUpdateForm');

        // --- 2. PASSWORD FORM INTERCEPT FOR TUTORIAL ---
        if (passForm) {
            passForm.addEventListener('submit', function() {
                if (isFirstLogin) {
                    localStorage.setItem('berc_pending_signature_tutorial', 'true');
                }
            });
        }

        // --- 3. FIRST LOGIN SECURITY LOCKDOWN & PASSWORD MODAL LOGIC ---
        const openBtn = document.getElementById('openPasswordModal');
        const closeBtn = document.getElementById('closePasswordModal');
        const cancelBtn = document.getElementById('cancelPasswordModal');

        const closeModal = () => {
            if (!isFirstLogin && passModal) passModal.classList.add('hidden');
        };

        if (isFirstLogin) {
            if (passModal) passModal.classList.remove('hidden');
            if (closeBtn) closeBtn.style.display = 'none';
            if (cancelBtn) cancelBtn.style.display = 'none';

            document.querySelectorAll('a, button:not(#openPasswordModal, .toggle-password, button[type="submit"])').forEach(el => {
                el.style.pointerEvents = 'none';
                el.style.opacity = '0.5';
            });

            if (passModal) {
                passModal.addEventListener('click', (e) => e.stopPropagation(), true);
            }

            const jsToast = document.getElementById('js-toast');
            const jsToastMessage = document.getElementById('js-toast-message');
            if (jsToast && jsToastMessage) {
                jsToastMessage.textContent = "Security Requirement: Please change your default password to continue.";
                jsToast.classList.remove('hidden', 'translate-x-full', 'opacity-0');
                jsToast.classList.add('translate-x-0', 'opacity-100');
            }
        } else {
            const userId = @json(auth()->id());
            localStorage.removeItem('berc_tutorial_step_' + userId);

            if (openBtn) openBtn.addEventListener('click', () => passModal.classList.remove('hidden'));
            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
            if (passModal) {
                passModal.addEventListener('click', (e) => {
                    if (e.target === passModal) closeModal();
                });
            }
        }

        // --- 4. SIGNATURE TUTORIAL ACTIVATION ---
        if (!isFirstLogin && localStorage.getItem('berc_pending_signature_tutorial') === 'true') {
            const tutorialBackdrop = document.getElementById('tutorialBackdrop');
            const tooltip = document.getElementById('signatureTutorialTooltip');
            const wrapper = document.getElementById('signatureTutorialWrapper');
            const dismissBtn = document.getElementById('dismissTutorialBtn');
            const openSigBtn = document.getElementById('openSignatureModal');

            // Only trigger if they don't already have a signature loaded
            if (tutorialBackdrop && tooltip && wrapper && openSigBtn) {
                tutorialBackdrop.classList.remove('hidden');
                tooltip.classList.remove('hidden');
                wrapper.classList.add('tutorial-active', 'ring-4', 'ring-brand-red', 'ring-offset-2');

                const finishTutorial = () => {
                    localStorage.removeItem('berc_pending_signature_tutorial');
                    tutorialBackdrop.classList.add('hidden');
                    tooltip.classList.add('hidden');
                    wrapper.classList.remove('tutorial-active', 'ring-4', 'ring-brand-red', 'ring-offset-2');
                };

                dismissBtn.addEventListener('click', () => {
                    finishTutorial();
                    if (openSigBtn) openSigBtn.click();
                });

                tutorialBackdrop.addEventListener('click', finishTutorial);
            } else {
                localStorage.removeItem('berc_pending_signature_tutorial');
            }
        }

        // --- 5. SIGNATURE UPLOAD MODAL LOGIC ---
        const sigModal = document.getElementById('signatureModal');
        const openSigBtn = document.getElementById('openSignatureModal');
        const agreeCheckbox = document.getElementById('agreeSignatureTos');
        const uploadForm = document.getElementById('signatureUploadForm');

        const closeSignatureModal = () => {
            if (sigModal) {
                sigModal.classList.add('hidden');
                if (agreeCheckbox) agreeCheckbox.checked = false;
                if (uploadForm) {
                    uploadForm.classList.add('hidden');
                    uploadForm.reset();
                }
            }
        };

        if (openSigBtn && !isFirstLogin) {
            openSigBtn.addEventListener('click', () => sigModal.classList.remove('hidden'));
        }

        document.getElementById('closeSignatureModalBtn')?.addEventListener('click', closeSignatureModal);
        document.getElementById('cancelSignatureModal')?.addEventListener('click', closeSignatureModal);

        if (agreeCheckbox && uploadForm) {
            agreeCheckbox.addEventListener('change', function() {
                if (this.checked) uploadForm.classList.remove('hidden');
                else uploadForm.classList.add('hidden');
            });
        }

        // --- 6. REMOVE SIGNATURE MODAL LOGIC ---
        const removeModal = document.getElementById('removeSigModal');
        const openRemoveBtn = document.getElementById('openRemoveSigModal');
        const cancelRemoveBtn = document.getElementById('cancelRemoveSig');

        if (openRemoveBtn) openRemoveBtn.addEventListener('click', () => removeModal.classList.remove('hidden'));
        if (cancelRemoveBtn) cancelRemoveBtn.addEventListener('click', () => removeModal.classList.add('hidden'));

        // --- 7. TOGGLE PASSWORD VISIBILITY ---
        const togglePasswordBtns = document.querySelectorAll('.toggle-password');
        togglePasswordBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const eyePaths = this.querySelectorAll('.eye-path');
                const eyeSlashPath = this.querySelector('.eye-slash-path');

                if (input.type === 'password') {
                    input.type = 'text';
                    eyePaths.forEach(path => path.classList.add('hidden'));
                    eyeSlashPath.classList.remove('hidden');
                } else {
                    input.type = 'password';
                    eyePaths.forEach(path => path.classList.remove('hidden'));
                    eyeSlashPath.classList.add('hidden');
                }
            });
        });

        // --- 8. LARAVEL VALIDATION ERROR HANDLING (Re-open Modals) ---
        @if($errors->hasAny(['current_password', 'password', 'new_password', 'password_confirmation']))
            if (passModal) passModal.classList.remove('hidden');
        @endif

        @if($errors->has('signature_image'))
            if (sigModal) {
                sigModal.classList.remove('hidden');
                if (agreeCheckbox && uploadForm) {
                    agreeCheckbox.checked = true;
                    uploadForm.classList.remove('hidden');
                }
            }
        @endif

        // --- 9. TOAST LOGIC ---
        const toasts = document.querySelectorAll('.toast-element');
        toasts.forEach(toast => {
            const closeToastBtn = toast.querySelector('.close-toast-btn');
            const hideToast = () => {
                toast.classList.remove('translate-x-0', 'opacity-100');
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => { toast.style.display = 'none'; }, 500);
            };
            const toastTimer = setTimeout(hideToast, 4000);
            if (closeToastBtn) {
                closeToastBtn.addEventListener('click', () => {
                    clearTimeout(toastTimer);
                    hideToast();
                });
            }
        });

    });
</script>
</body>
</html>
