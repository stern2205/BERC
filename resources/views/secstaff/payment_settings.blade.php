<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSU Ethics Review Committee – Payment Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/functions.js') }}" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>
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
        body { font-family: 'Inter', sans-serif; overflow-y: scroll; }
        :root { --bsu-dark: #213C71; --brand-red: #D32F2F; }

        .nav-tab-active { border-bottom: 3px solid #D32F2F; color: #213C71; }

        .section-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
        }
        .section-header {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            border-bottom: 1px solid #e5e7eb;
            background: #fafafa;
            gap: 10px;
        }
        .section-header .red-bar {
            width: 3px; height: 16px;
            background: var(--brand-red);
            border-radius: 2px;
            flex-shrink: 0;
        }
        .section-header h2 {
            font-size: 11px; font-weight: 800;
            text-transform: uppercase; letter-spacing: .08em;
            color: var(--bsu-dark);
        }

        .methods-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
            gap: 16px;
            padding: 20px;
        }

        /* ── Dynamic Inputs & Add Buttons ── */
        .input-row { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .input-row .form-input { flex: 1; margin-bottom: 0; }
        .btn-remove { background: none; border: none; color: #9ca3af; font-size: 16px; font-weight: bold; cursor: pointer; transition: color 0.2s ease; padding: 5px; display: flex; align-items: center; justify-content: center; }
        .btn-remove:hover { color: #dc2626; }
        .btn-add { background: transparent; border: 2px dashed #d1d5db; border-radius: 8px; color: #1e3a8a; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 12px; width: 100%; cursor: pointer; transition: all 0.2s ease; text-align: center; }
        .btn-add:hover { border-color: #1e3a8a; background: #eff6ff; }


        /* ADD NEW CARD */
        .add-new-card {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 130px;
            cursor: pointer;
            background: #fafafa;
            transition: border-color .2s, background .2s, box-shadow .2s;
        }
        .add-new-card:hover {
            border-color: var(--bsu-dark);
            background: #f0f4ff;
            box-shadow: 0 0 0 3px rgba(33,60,113,.07);
        }
        .add-new-card .plus-icon {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex; align-items: center; justify-content: center;
            transition: background .2s;
        }
        .add-new-card:hover .plus-icon { background: var(--bsu-dark); }
        .add-new-card:hover .plus-icon svg { stroke: #fff; }
        .add-new-card .plus-label {
            font-size: 11px; font-weight: 800;
            text-transform: uppercase; letter-spacing: .07em;
            color: #9ca3af;
        }
        .add-new-card:hover .plus-label { color: var(--bsu-dark); }

        .method-card {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            transition: border-color .25s, box-shadow .25s;
            background: #fff;
        }
        .method-card.enabled  { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,.07); }
        .method-card.disabled { border-color: #e5e7eb; }

        .method-card-top {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            gap: 12px;
        }

        .method-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 900; color: #fff;
            flex-shrink: 0;
            transition: background .2s;
            overflow: hidden;
        }
        .method-icon img {
            width: 100%; height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .method-info { flex: 1; min-width: 0; }
        .method-name { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; }
        .method-account  { font-size: 14px; font-weight: 800; color: #111827; margin-top: 1px; }
        .method-acctname { font-size: 11px; color: #6b7280; margin-top: 1px; }

        /* Top-right action buttons */
        .card-actions { display: flex; flex-direction: column; align-items: center; gap: 6px; flex-shrink: 0; }
        .toggle-wrap  { display: flex; flex-direction: column; align-items: center; gap: 4px; }
        .toggle-switch { position: relative; width: 44px; height: 24px; cursor: pointer; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
        .slider { position: absolute; inset: 0; background: #d1d5db; border-radius: 999px; transition: background .2s; }
        .slider::before {
            content: ''; position: absolute;
            width: 18px; height: 18px;
            left: 3px; top: 3px;
            background: #fff; border-radius: 50%;
            transition: transform .2s;
            box-shadow: 0 1px 3px rgba(0,0,0,.2);
        }
        .toggle-switch input:checked + .slider { background: #10b981; }
        .toggle-switch input:checked + .slider::before { transform: translateX(20px); }

        .status-badge {
            font-size: 9px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .05em; padding: 3px 8px; border-radius: 999px;
        }
        .badge-enabled  { background: #dcfce7; color: #166534; }
        .badge-disabled { background: #fee2e2; color: #991b1b; }

        .delete-btn-small {
            width: 26px; height: 26px;
            border-radius: 6px;
            border: 1.5px solid #fca5a5;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: background .15s, border-color .15s;
        }
        .delete-btn-small:hover { background: #fee2e2; border-color: #ef4444; }
        .delete-btn-small svg  { stroke: #ef4444; }

        /* ── Card Body (Locked / Unlocked states) ── */
        .method-card-body {
            padding: 12px 16px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            transition: background .2s;
        }
        .method-card-body.unlocked {
            background: #fffef0;
            border-top-color: #fbbf24;
        }

        /* Locked overlay banner */
        .locked-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 8px 10px;
            background: #f3f4f6;
            border: 1.5px dashed #d1d5db;
            border-radius: 8px;
            cursor: pointer;
            transition: background .18s, border-color .18s, box-shadow .18s;
            user-select: none;
        }
        .locked-banner:hover {
            background: #eef2ff;
            border-color: var(--bsu-dark);
            box-shadow: 0 0 0 3px rgba(33,60,113,.07);
        }
        .locked-banner-text {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .07em;
            color: #9ca3af;
            transition: color .18s;
        }
        .locked-banner:hover .locked-banner-text { color: var(--bsu-dark); }
        .locked-banner-icon {
            width: 28px; height: 28px;
            border-radius: 7px;
            background: #e5e7eb;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: background .18s;
        }
        .locked-banner:hover .locked-banner-icon { background: var(--bsu-dark); }
        .locked-banner:hover .locked-banner-icon svg { stroke: #fff; }

        /* Edit form (hidden by default) */
        .edit-form { display: none; }
        .edit-form.visible { display: block; }

        /* Unlock header inside edit form */
        .edit-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .edit-header-label {
            display: flex; align-items: center; gap: 6px;
            font-size: 10px; font-weight: 800;
            text-transform: uppercase; letter-spacing: .07em;
            color: #b45309;
        }
        .edit-header-label svg { stroke: #b45309; }
        .cancel-edit-btn {
            display: flex; align-items: center; gap: 4px;
            font-size: 9px; font-weight: 800;
            text-transform: uppercase; letter-spacing: .06em;
            color: #9ca3af;
            background: none; border: none; cursor: pointer;
            padding: 4px 8px; border-radius: 5px;
            transition: background .15s, color .15s;
            font-family: 'Inter', sans-serif;
        }
        .cancel-edit-btn:hover { background: #fee2e2; color: #ef4444; }

        .field-row { margin-bottom: 8px; }
        .field-row:last-of-type { margin-bottom: 0; }
        .field-label {
            font-size: 9px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .07em; color: #9ca3af; margin-bottom: 3px;
        }
        .field-input {
            width: 100%; padding: 7px 10px;
            font-size: 12px; font-weight: 600;
            border: 1.5px solid #d1d5db;
            border-radius: 6px; outline: none;
            transition: border-color .15s;
            background: #fff;
            font-family: 'Inter', sans-serif;
        }
        .field-input:focus { border-color: var(--bsu-dark); }

        .save-btn {
            width: 100%; margin-top: 10px; padding: 8px;
            background: var(--bsu-dark); color: #fff;
            border: none; border-radius: 7px;
            font-size: 11px; font-weight: 800;
            text-transform: uppercase; letter-spacing: .06em;
            cursor: pointer; transition: opacity .15s;
            font-family: 'Inter', sans-serif;
        }
        .save-btn:hover { opacity: .85; }

        /* ── Color Picker Swatches ── */
        .color-swatches { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 4px; }
        .swatch {
            width: 26px; height: 26px; border-radius: 6px;
            cursor: pointer; border: 2px solid transparent;
            transition: transform .15s, border-color .15s;
        }
        .swatch:hover { transform: scale(1.15); }
        .swatch.selected { border-color: #111827; transform: scale(1.1); }

        /* ── Image Upload Zone ── */
        .img-upload-zone {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            background: #fafafa;
            transition: border-color .2s, background .2s;
            position: relative;
            overflow: hidden;
        }
        .img-upload-zone:hover {
            border-color: var(--bsu-dark);
            background: #f0f4ff;
        }
        .img-upload-zone input[type="file"] {
            position: absolute; inset: 0;
            opacity: 0; cursor: pointer; width: 100%; height: 100%;
        }
        .img-upload-zone .upload-placeholder {
            display: flex; flex-direction: column;
            align-items: center; gap: 5px;
            pointer-events: none;
        }
        .img-upload-zone .upload-icon-wrap {
            width: 32px; height: 32px; border-radius: 8px;
            background: #e5e7eb;
            display: flex; align-items: center; justify-content: center;
            transition: background .2s;
        }
        .img-upload-zone:hover .upload-icon-wrap { background: var(--bsu-dark); }
        .img-upload-zone:hover .upload-icon-wrap svg { stroke: #fff; }
        .img-upload-zone .upload-text {
            font-size: 10px; font-weight: 700; color: #9ca3af;
            text-transform: uppercase; letter-spacing: .06em;
        }
        .img-upload-zone .upload-sub {
            font-size: 9px; color: #c4c9d4; font-weight: 600;
        }

        /* Preview thumbnail inside upload zone */
        .img-preview-thumb {
            display: none;
            width: 52px; height: 52px; border-radius: 10px;
            object-fit: cover;
            margin: 0 auto 6px;
            border: 2px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0,0,0,.1);
        }
        .img-preview-thumb.visible { display: block; }

        /* Remove image button */
        .remove-img-btn {
            display: none;
            align-items: center; justify-content: center;
            gap: 4px;
            font-size: 9px; font-weight: 800;
            text-transform: uppercase; letter-spacing: .06em;
            color: #ef4444;
            background: #fee2e2; border: 1.5px solid #fca5a5;
            border-radius: 5px; padding: 3px 8px;
            cursor: pointer; margin-top: 6px;
            transition: background .15s;
            font-family: 'Inter', sans-serif;
            width: 100%;
            pointer-events: auto;
            position: relative; z-index: 2;
        }
        .remove-img-btn.visible { display: flex; }
        .remove-img-btn:hover { background: #fecaca; }

        /* Edit form inline image upload */
        .edit-img-zone {
            border: 1.5px dashed #d1d5db;
            border-radius: 7px;
            padding: 8px 10px;
            display: flex; align-items: center; gap: 10px;
            background: #fff;
            cursor: pointer;
            transition: border-color .2s, background .2s;
            position: relative;
            overflow: hidden;
        }
        .edit-img-zone:hover {
            border-color: var(--bsu-dark);
            background: #f0f4ff;
        }
        .edit-img-zone input[type="file"] {
            position: absolute; inset: 0;
            opacity: 0; cursor: pointer; width: 100%; height: 100%;
            z-index: 1;
        }
        .edit-img-thumb {
            width: 36px; height: 36px; border-radius: 8px;
            object-fit: cover;
            border: 1.5px solid #e5e7eb;
            flex-shrink: 0;
            pointer-events: none;
        }
        .edit-img-placeholder {
            width: 36px; height: 36px; border-radius: 8px;
            background: #e5e7eb;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            pointer-events: none;
        }
        .edit-img-info { flex: 1; pointer-events: none; }
        .edit-img-label {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .06em;
            color: #6b7280;
        }
        .edit-img-sub { font-size: 9px; color: #9ca3af; margin-top: 1px; }
        .edit-img-remove {
            font-size: 9px; font-weight: 800;
            text-transform: uppercase; letter-spacing: .05em;
            color: #ef4444; background: #fee2e2;
            border: 1.5px solid #fca5a5; border-radius: 5px;
            padding: 3px 7px; cursor: pointer;
            transition: background .15s;
            flex-shrink: 0;
            z-index: 2; position: relative;
            font-family: 'Inter', sans-serif;
        }
        .edit-img-remove:hover { background: #fecaca; }
        .edit-img-remove.hidden { display: none; }

        /* ── Modal ── */
        .modal-backdrop {
            position: fixed; inset: 0; z-index: 100;
            background: rgba(0,0,0,.5);
            backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            padding: 16px;
            opacity: 0; pointer-events: none;
            transition: opacity .25s;
        }
        .modal-backdrop.open { opacity: 1; pointer-events: auto; }
        .modal-box {
            background: #fff; border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            width: 100%; max-width: 420px;
            transform: scale(.95) translateY(12px);
            transition: transform .25s;
            overflow: hidden;
        }
        .modal-backdrop.open .modal-box { transform: scale(1) translateY(0); }
        .modal-header {
            padding: 20px 24px 16px;
            border-bottom: 1px solid #e5e7eb;
            display: flex; align-items: center; justify-content: space-between;
        }
        .modal-header h3 {
            font-size: 14px; font-weight: 900;
            text-transform: uppercase; letter-spacing: .06em;
            color: var(--bsu-dark);
        }
        .modal-close-btn {
            width: 28px; height: 28px; border-radius: 7px;
            border: 1.5px solid #e5e7eb; background: #f9fafb;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: background .15s;
        }
        .modal-close-btn:hover { background: #fee2e2; border-color: #fca5a5; }
        .modal-body { padding: 20px 24px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 8px; }

        .btn-cancel {
            padding: 8px 16px; border-radius: 8px;
            border: 1.5px solid #e5e7eb; background: #fff;
            font-size: 11px; font-weight: 800; text-transform: uppercase;
            letter-spacing: .06em; color: #6b7280; cursor: pointer;
            transition: background .15s;
            font-family: 'Inter', sans-serif;
        }
        .btn-cancel:hover { background: #f3f4f6; }

        .btn-primary {
            padding: 8px 20px; border-radius: 8px;
            border: none; background: var(--bsu-dark); color: #fff;
            font-size: 11px; font-weight: 800; text-transform: uppercase;
            letter-spacing: .06em; cursor: pointer;
            transition: opacity .15s;
            font-family: 'Inter', sans-serif;
        }
        .btn-primary:hover { opacity: .85; }

        .btn-danger {
            padding: 8px 20px; border-radius: 8px;
            border: none; background: #D32F2F; color: #fff;
            font-size: 11px; font-weight: 800; text-transform: uppercase;
            letter-spacing: .06em; cursor: pointer;
            transition: opacity .15s;
            font-family: 'Inter', sans-serif;
        }
        .btn-danger:hover { opacity: .85; }

        /* ── Toast ── */
        .toast {
            position: fixed; bottom: 24px; right: 24px;
            background: #111827; color: #fff;
            padding: 12px 20px; border-radius: 10px;
            font-size: 12px; font-weight: 700;
            box-shadow: 0 8px 24px rgba(0,0,0,.3);
            z-index: 9999; opacity: 0;
            transform: translateY(10px);
            transition: all .3s;
            pointer-events: none;
        }
        .toast.show { opacity: 1; transform: translateY(0); }

        /* ── Icon preview in add modal ── */
        .icon-preview {
            width: 48px; height: 48px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 900; color: #fff;
            overflow: hidden;
            flex-shrink: 0;
        }
        .icon-preview img { width: 100%; height: 100%; object-fit: cover; }

        /* ── Unlock animation ── */
        @keyframes unlockPulse {
            0%   { box-shadow: 0 0 0 0 rgba(251,191,36,.5); }
            70%  { box-shadow: 0 0 0 8px rgba(251,191,36,0); }
            100% { box-shadow: 0 0 0 0 rgba(251,191,36,0); }
        }
        .method-card-body.unlocked {
            animation: unlockPulse .45s ease-out;
        }

        /* color section dimmed when image present */
        .color-section-wrap.has-image { opacity: .4; pointer-events: none; }
        .color-section-wrap.has-image::after {
            content: 'Color disabled when image is used';
            display: block;
            font-size: 9px; font-weight: 700; color: #b45309;
            text-transform: uppercase; letter-spacing: .05em;
            margin-top: 4px;
        }
        /* Custom styling for the tutorial popover to match BSU Theme */
        .driver-popover { font-family: 'Inter', sans-serif !important; border-radius: 12px !important; border: 1px solid #E5E7EB !important; padding: 20px !important; }
        .driver-popover-title { color: #213C71 !important; font-weight: 900 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; font-size: 14px !important; }
        .driver-popover-description { color: #6B7280 !important; font-weight: 500 !important; font-size: 12px !important; margin-top: 8px !important; line-height: 1.5 !important; }
        .driver-popover-footer button { border-radius: 8px !important; font-weight: 700 !important; font-size: 11px !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; padding: 8px 12px !important; }
        .driver-popover-next-btn { background-color: #D32F2F !important; color: white !important; border: none !important; text-shadow: none !important; transition: all 0.2s ease !important; }
        .driver-popover-next-btn:hover { background-color: #b91c1c !important; }
        .driver-popover-prev-btn { background-color: #F3F4F6 !important; color: #4B5563 !important; border: none !important; }
        .driver-popover-prev-btn:hover { background-color: #E5E7EB !important; }
    </style>
</head>
<header class="w-full bg-[#ffffff] border-b border-gray-200 sticky top-0 z-50">
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
        <div class="flex items-center gap-3">
            <div class="date-block">
                <p class="time" id="sec-clock">
                    <?php echo date('h:i A'); ?> | <?php echo strtoupper(date('l')); ?>
                </p>
                <p class="date"><?php echo date('d/m/Y'); ?></p>
            </div>
            <button id="sec-hamburger"
                    class="sm:hidden flex flex-col justify-center items-center gap-[5px] w-9 h-9 rounded-lg border border-gray-200 bg-white"
                    aria-label="Toggle menu">
                <span class="block w-5 h-[2px] bg-bsu-dark rounded transition-all duration-200" id="hb1"></span>
                <span class="block w-5 h-[2px] bg-bsu-dark rounded transition-all duration-200" id="hb2"></span>
                <span class="block w-5 h-[2px] bg-bsu-dark rounded transition-all duration-200" id="hb3"></span>
            </button>
        </div>
    </div>

    <!-- Desktop nav bar -->
    <div class="border-t border-b border-gray-200 bg-[#FCFCFC] hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-gray-500">
            <div class="flex space-x-1">
                <a href="{{ route('dashboard') }}"
                class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                {{ request()->routeIs('dashboard') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('secstaff.applications') }}"
                class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                {{ request()->routeIs('secstaff.applications') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('secstaff.applications') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span>Applications</span>
                </a>

                <a href="{{ route('secstaff.calendar') }}"
                class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                {{ request()->routeIs('secstaff.calendar') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('secstaff.calendar') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Calendar</span>
                </a>

                <a href="{{ route('secstaff.history') }}"
                class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                {{ request()->routeIs('secstaff.history') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('secstaff.history') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.05 11a9 9 0 1 0 .5-3M3 4v4h4"/>
                    </svg>
                    <span>History</span>
                </a>

                <a href="{{ route('secstaff.payment_settings') }}"
                class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                {{ request()->routeIs('secstaff.payment_settings') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('secstaff.payment_settings') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span>Payment Settings</span>
                </a>
                <a href="#" class="flex items-center space-x-2 px-6 py-4 hover:text-bsu-dark transition-colors">
                    <span>    </span>
                </a>
            </div>

            <div class="flex items-center space-x-6 border-l border-gray-200 pl-6 py-4">
                <button type="button"
                        onclick="startManualTutorial()"
                        class="flex items-center gap-2 transition-all hover:-translate-y-0.5 text-gray-500 hover:text-bsu-dark">
                    <svg class="w-4 h-4 shrink-0 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 1.823-2 3.272-2 1.933 0 3.5 1.343 3.5 3 0 1.305-.973 2.416-2.333 2.83-.727.221-1.167.874-1.167 1.67M12 18h.01M12 3a9 9 0 100 18 9 9 0 000-18z" />
                    </svg>
                    <span>VIEW TUTORIAL</span>
                </button>
                <a href="{{ route('settings') }}"
                class="flex items-center gap-2 transition-all hover:-translate-y-0.5 {{ request()->routeIs('settings') ? 'text-bsu-dark font-black' : 'text-gray-500 hover:text-bsu-dark' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Settings</span>
                </a>

                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>

                <button type="button" onclick="showLogoutModal()"
                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-xs font-bold uppercase transition-all duration-300 hover:-translate-y-1 hover:shadow-md active:scale-95 shadow-sm">
                    Sign Out
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile nav drawer -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 bg-[#FCFCFC]">
        <div class="flex flex-col text-[11px] font-bold uppercase tracking-wider text-gray-500 divide-y divide-gray-100">
            <a href="#" class="flex items-center space-x-3 text-bsu-dark px-5 py-3.5">
                <svg class="w-4 h-4 text-brand-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('secstaff.applications') }}" class="flex items-center space-x-3 px-5 py-3.5 hover:text-bsu-dark">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span>Applications</span>
            </a>
            <a href="{{ route('secstaff.calendar') }}" class="flex items-center space-x-3 px-5 py-3.5 hover:text-bsu-dark">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span>Calendar</span>
            </a>
            <a href="{{ route('secstaff.history') }}" class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest text-gray-500 hover:text-bsu-dark transition border-b-[3px] border-transparent hover:border-brand-red">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.05 11a9 9 0 1 0 .5-3M3 4v4h4"/>
                </svg>
                History
            </a>
            <a href="{{ route('secstaff.payment_settings') }}" class="flex items-center space-x-3 px-5 py-3.5 hover:text-bsu-dark">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span>Payment Settings</span>
            </a>
            <div class="flex items-center space-x-6 border-l border-gray-200 pl-6 py-4">
                <a href="{{ route('settings') }}"
                class="flex items-center gap-2 transition-all hover:-translate-y-0.5 {{ request()->routeIs('settings') ? 'text-bsu-dark font-black' : 'text-gray-500 hover:text-bsu-dark' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Settings</span>
                </a>

                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>

                <button type="button" onclick="showLogoutModal()"
                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-xs font-bold uppercase transition-all duration-300 hover:-translate-y-1 hover:shadow-md active:scale-95 shadow-sm">
                    Sign Out
                </button>
            </div>
            <!-- Date on mobile -->
            <div class="px-5 py-3 bg-white">
                <p class="text-[11px] font-bold text-bsu-dark uppercase tracking-wide">12:00 AM | Monday</p>
                <p class="text-[10px] font-bold text-brand-red">16/02/2026</p>
            </div>
        </div>
    </div>
</header>

<main class="max-w-7xl mx-auto px-4 sm:px-4 py-4 sm:py-4">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative mb-4">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/background.jpg') }}" alt="Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-bsu-dark/70"></div>
        </div>
        <div class="relative z-10 p-4 sm:p-8 flex flex-col sm:flex-row items-center gap-4 sm:gap-6">
            <div class="shrink-0">
                <div class="bg-white/20 backdrop-blur-sm p-1 rounded-2xl border border-white/20 shadow-lg">
                    <img src="{{ asset($user->profile_image ?? 'profiles/default.png') }}" alt="Student Photo"
                            class="w-16 h-16 sm:w-24 sm:h-24 object-cover bg-gray-300 rounded-xl">
                </div>
            </div>
            <div class="flex-1 text-white text-center sm:text-left">
                <p class="text-[10px] font-bold text-gray-200 uppercase tracking-widest">Secretarial Staff</p>
                <h2 class="text-xl sm:text-2xl font-black uppercase tracking-tight">{{ $user->name }}</h2>
                <div class="flex flex-col sm:flex-row sm:space-x-12 gap-2 sm:gap-0 mt-2 sm:mt-3 items-center sm:items-start">
                    <div>
                        <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Account ID</p>
                        <p class="text-xs font-bold tracking-wide">{{ $user->id }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Email Address</p>
                        <p class="text-xs font-bold tracking-wide">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="tour-payment-info" class="flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 mb-4 text-[11px] font-semibold text-blue-700">
        <svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r=".5" fill="currentColor"/>
        </svg>
        You can add, edit, or delete payment methods. Click <strong class="mx-1">✏️ Edit Details</strong> on any card to unlock and modify its information, account number, account name, or upload a logo image. Disabling a method grays it out with an "Unavailable" watermark on the researcher's payment page.
    </div>

    <div id="tour-payment-grid" class="section-card">
        <div class="section-header" style="justify-content: space-between;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div class="red-bar"></div>
                <h2>Online Payment Methods</h2>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Add · Toggle · Edit · Delete</span>
        </div>
        <div class="methods-grid" id="methods-grid"></div>
    </div>

</main>

<div id="confirm-modal" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Confirm Changes</h3>
            <button class="modal-close-btn" onclick="closeModal('confirm-modal')">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-4">
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-2">Updating</p>
                <p class="text-sm font-black text-bsu-dark uppercase" id="modal-method-name"></p>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">New Account No.</p>
                    <p class="text-xs font-bold text-gray-800" id="modal-preview-acct">—</p>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">New Account Name</p>
                    <p class="text-xs font-bold text-gray-800" id="modal-preview-name">—</p>
                </div>
            </div>
            <div id="modal-logo-row" style="display:none;" class="mb-3">
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">New Logo</p>
                <img id="modal-preview-logo" src="" alt="Logo" style="width:44px;height:44px;border-radius:8px;object-fit:cover;border:1.5px solid #e5e7eb;">
                <p id="modal-logo-removed-txt" style="display:none; font-size:11px; font-weight:700; color:#ef4444;">Logo will be removed</p>
            </div>
            <p class="text-[11px] text-gray-500 mt-2">Are you sure you want to save these changes?</p>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('confirm-modal')">Cancel</button>
            <button class="btn-primary" onclick="confirmSave()">
                <svg style="display:inline;width:12px;height:12px;margin-right:4px;vertical-align:-2px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Confirm Save
            </button>
        </div>
    </div>
</div>

<div id="delete-modal" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <h3 style="color:#D32F2F;">Delete Payment Method</h3>
            <button class="modal-close-btn" onclick="closeModal('delete-modal')">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                <svg class="w-5 h-5 shrink-0 text-brand-red mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-xs font-semibold text-red-700">This will permanently remove <strong id="delete-method-name"></strong> from the payment options. Researchers will no longer see this method.</p>
            </div>
            <p class="text-xs text-gray-500">This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('delete-modal')">Cancel</button>
            <button class="btn-danger" onclick="confirmDelete()">Delete</button>
        </div>
    </div>
</div>

<div id="add-modal" class="modal-backdrop">
    <div class="modal-box" style="max-width:480px;">
        <div class="modal-header">
            <h3>Add Payment Method</h3>
            <button class="modal-close-btn" onclick="closeModal('add-modal')">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body" style="max-height:70vh;overflow-y:auto;">

            <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-xl p-3 mb-4">
                <div class="icon-preview" id="add-icon-preview" style="background:#213C71;">
                    <img id="add-icon-img-preview" src="" alt="" style="display:none;width:100%;height:100%;object-fit:cover;">
                    <span id="add-icon-text-preview">?</span>
                </div>
                <div>
                    <p class="text-xs font-black uppercase text-bsu-dark tracking-wide" id="add-name-preview">New Method</p>
                    <p class="text-[11px] text-gray-400">Preview</p>
                </div>
            </div>

            <div class="field-row">
                <div class="field-label">Bank / Method Name <span class="text-red-500">*</span></div>
                <input class="field-input" id="add-name" placeholder="e.g. Landbank, PayPal" oninput="updateAddPreview()">
            </div>
            <div class="field-row">
                <div class="field-label">Short Icon Label <span class="text-red-500">*</span> <span id="add-icon-label-note" class="text-[9px] text-amber-500 font-semibold normal-case">(hidden when logo image is set)</span></div>
                <input class="field-input" id="add-icon" placeholder="e.g. LB, PP (max 3 chars)" maxlength="3" oninput="updateAddPreview()">
            </div>
            <div class="field-row">
                <div class="field-label">Account Number <span class="text-red-500">*</span></div>
                <input class="field-input" id="add-acct" placeholder="Account number">
            </div>
            <div class="field-row">
                <div class="field-label">Account Name <span class="text-red-500">*</span></div>
                <input class="field-input" id="add-acctname" placeholder="Account holder name">
            </div>

            <div class="field-row" style="margin-top:10px;">
                <div class="field-label">
                    Logo / Icon Image
                    <span style="color:#6b7280;text-transform:none;letter-spacing:0;font-weight:600;font-size:9px;"> — optional, replaces the color icon</span>
                </div>
                <div class="img-upload-zone" id="add-upload-zone">
                    <input type="file" accept=".png, .jpg, .jpeg, .gif" id="add-logo-input" onchange="handleAddLogoChange(this)">
                    <div class="upload-placeholder">
                        <img class="img-preview-thumb" id="add-logo-thumb" src="" alt="Logo preview">
                        <div class="upload-icon-wrap">
                            <svg width="16" height="16" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                        </div>
                        <p class="upload-text" id="add-upload-text">Upload Logo</p>
                        <p class="upload-sub">PNG, JPG, GIF · Max 2MB</p>
                    </div>
                </div>
                <button class="remove-img-btn" id="add-remove-logo" onclick="removeAddLogo(event)">
                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Remove Logo
                </button>
            </div>

            <div class="field-row color-section-wrap" id="add-color-section" style="margin-top:10px;">
                <div class="field-label">Icon Background Color <span style="color:#9ca3af;font-weight:600;text-transform:none;letter-spacing:0;font-size:9px;">(used when no logo)</span></div>
                <div class="color-swatches" id="color-swatches"></div>
            </div>

            <div class="field-row" style="margin-top:10px;">
                <div class="field-label">Status</div>
                <div style="display:flex;gap:10px;margin-top:4px;">
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;font-weight:700;">
                        <input type="radio" name="add-status" value="true" checked style="accent-color:var(--bsu-dark)"> Active
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;font-weight:700;">
                        <input type="radio" name="add-status" value="false" style="accent-color:var(--bsu-dark)"> Inactive
                    </label>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('add-modal')">Cancel</button>
            <button class="btn-primary" onclick="confirmAdd()">
                <svg style="display:inline;width:12px;height:12px;margin-right:4px;vertical-align:-2px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Method
            </button>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

<div id="logout-modal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[10000] hidden flex items-center justify-center transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden transform transition-all">
        <div class="p-6 text-center">
            <svg class="mx-auto mb-4 text-brand-red w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>

            <h3 class="mb-2 text-[13px] font-bold text-bsu-dark uppercase tracking-wide">Sign Out?</h3>

            <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                Are you sure you want to end your current session?<br>
                You will need to log in again to access your dashboard.
            </p>

            <div class="flex justify-center gap-3">
                <button type="button" onclick="hideLogoutModal()"
                    class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest text-gray-500 hover:bg-gray-100 rounded-lg transition">
                    Cancel
                </button>

                <button type="button" onclick="confirmLogout()"
                    class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest bg-brand-red hover:bg-red-700 text-white rounded-lg transition shadow-md">
                    Yes, Sign Out
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // ══════════════════════════════════════════════════════════════
    // CLOCK
    // ══════════════════════════════════════════════════════════════
    var secHamburger = document.getElementById('sec-hamburger');
    var secMenu = document.getElementById('sec-mobile-menu');
    var menuOpen = false;
    secHamburger.addEventListener('click', function() {
        menuOpen = !menuOpen;
        secMenu.classList.toggle('hidden', !menuOpen);
        document.getElementById('hb1').style.transform = menuOpen ? 'translateY(7px) rotate(45deg)' : '';
        document.getElementById('hb2').style.opacity   = menuOpen ? '0' : '1';
        document.getElementById('hb3').style.transform = menuOpen ? 'translateY(-7px) rotate(-45deg)' : '';
    });

    function updateSecClock() {
        var now = new Date();
        var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        var h = now.getHours(), m = now.getMinutes(), ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        var timeStr = (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m + ' ' + ampm + ' | ' + days[now.getDay()].toUpperCase();
        var d = now.getDate(), mo = now.getMonth() + 1, y = now.getFullYear();
        var dateStr = (d < 10 ? '0' : '') + d + '/' + (mo < 10 ? '0' : '') + mo + '/' + y;
        ['sec-clock','sec-clock-m'].forEach(function(id){ var el=document.getElementById(id); if(el) el.textContent=timeStr; });
        ['sec-date','sec-date-m'].forEach(function(id){ var el=document.getElementById(id); if(el) el.textContent=dateStr; });
    }
    updateSecClock();
    setInterval(updateSecClock, 1000);

    // ══════════════════════════════════════════════════════════════
    // COLOR PALETTE & STATE
    // ══════════════════════════════════════════════════════════════
    const COLOR_PALETTE = [
        { hex: '#0068b4', label: 'Blue'       },
        { hex: '#09ab72', label: 'Green'      },
        { hex: '#1c2b6a', label: 'Navy'       },
        { hex: '#e65c00', label: 'Orange'     },
        { hex: '#b41f1f', label: 'Red'        },
        { hex: '#5c2d91', label: 'Purple'     },
        { hex: '#213C71', label: 'BSU Dark'   },
        { hex: '#0f766e', label: 'Teal'       },
        { hex: '#b45309', label: 'Amber'      },
        { hex: '#374151', label: 'Charcoal'   },
        { hex: '#be185d', label: 'Pink'       },
        { hex: '#166534', label: 'Forest'     },
    ];
    let selectedColor = COLOR_PALETTE[0].hex;

    // Data from database
    let paymentMethods = [];

    // Modal state trackers
    let pendingSaveId      = null;
    let pendingSaveAcct    = '';
    let pendingSaveName    = '';
    let pendingSaveLogo    = null; // dataUrl for preview
    let pendingDeleteId    = null;
    const editStagedLogo   = {};   // id -> dataUrl | null | 'REMOVE'
    let addStagedLogo      = null; // dataUrl or null

    const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]').content;

    // ══════════════════════════════════════════════════════════════
    // FETCH DATA FROM LARAVEL
    // ══════════════════════════════════════════════════════════════
    async function fetchMethods() {
        try {
            const response = await fetch('/admin/payment-methods');

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const result = await response.json();
            if (result.success) {
                paymentMethods = result.data || [];
            }
        } catch (error) {
            console.error('Error fetching methods:', error);
            showToast('Failed to load payment methods.');
        } finally {
            // GUARANTEE the grid renders so the Add button never disappears
            renderGrid();
        }
    }

    // ══════════════════════════════════════════════════════════════
    // UI HELPERS (Swatches, Upload previews, etc.)
    // ══════════════════════════════════════════════════════════════
    function buildColorSwatches(containerId, currentColor, onSelect) {
        var container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = COLOR_PALETTE.map(function(c) {
            return '<div class="swatch' + (c.hex === currentColor ? ' selected' : '') +
                '" style="background:' + c.hex + ';" title="' + c.label +
                '" onclick="(' + onSelect.toString() + ')(\'' + c.hex + '\')"></div>';
        }).join('');
    }

    function selectColor(hex) {
        selectedColor = hex;
        buildColorSwatches('color-swatches', selectedColor, selectColor);
        document.getElementById('add-icon-preview').style.background = hex;
    }

    function buildAddColorSwatches() {
        buildColorSwatches('color-swatches', selectedColor, selectColor);
    }

    function handleAddLogoChange(input) {
        var file = input.files && input.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) { showToast('Image too large. Max 2MB.'); input.value = ''; return; }
        var reader = new FileReader();
        reader.onload = function(e) {
            addStagedLogo = e.target.result;
            var thumb = document.getElementById('add-logo-thumb');
            thumb.src = addStagedLogo;
            thumb.classList.add('visible');
            document.getElementById('add-upload-text').textContent = 'Change Logo';
            document.getElementById('add-remove-logo').classList.add('visible');
            document.getElementById('add-icon-img-preview').src = addStagedLogo;
            document.getElementById('add-icon-img-preview').style.display = 'block';
            document.getElementById('add-icon-text-preview').style.display = 'none';
            document.getElementById('add-color-section').classList.add('has-image');
        };
        reader.readAsDataURL(file);
    }

    function removeAddLogo(e) {
        e.stopPropagation();
        addStagedLogo = null;
        document.getElementById('add-logo-input').value = '';
        var thumb = document.getElementById('add-logo-thumb');
        thumb.src = '';
        thumb.classList.remove('visible');
        document.getElementById('add-upload-text').textContent = 'Upload Logo';
        document.getElementById('add-remove-logo').classList.remove('visible');
        document.getElementById('add-icon-img-preview').style.display = 'none';
        document.getElementById('add-icon-text-preview').style.display = '';
        document.getElementById('add-color-section').classList.remove('has-image');
    }

    function handleEditLogoChange(id, input) {
        var file = input.files && input.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) { showToast('Image too large. Max 2MB.'); input.value = ''; return; }
        var reader = new FileReader();
        reader.onload = function(e) {
            editStagedLogo[id] = e.target.result;
            refreshEditLogoZone(id);
        };
        reader.readAsDataURL(file);
    }

    function removeEditLogo(id, e) {
        if (e) e.stopPropagation();
        editStagedLogo[id] = 'REMOVE';
        var inp = document.getElementById('edit-logo-input-' + id);
        if (inp) inp.value = '';
        refreshEditLogoZone(id);
    }

    function refreshEditLogoZone(id) {
        var staged   = editStagedLogo[id];
        var m        = paymentMethods.find(function(x){ return x.id == id; });
        var current  = (staged === undefined) ? (m.logo_path ? '/' + m.logo_path : null) : (staged === 'REMOVE' ? null : staged);
        var hasLogo  = !!current;

        var thumb    = document.getElementById('edit-logo-thumb-' + id);
        var ph       = document.getElementById('edit-logo-ph-' + id);
        var label    = document.getElementById('edit-logo-label-' + id);
        var sub      = document.getElementById('edit-logo-sub-' + id);
        var removeBtn= document.getElementById('edit-logo-remove-' + id);

        if (hasLogo) {
            if (thumb) { thumb.src = current; thumb.style.display = 'block'; }
            if (ph) ph.style.display = 'none';
            if (label) label.textContent = 'Change Logo';
            if (sub) sub.textContent = 'Click to replace · ' + (staged && staged !== 'REMOVE' ? 'New upload staged' : 'Current logo');
            if (removeBtn) removeBtn.classList.remove('hidden');
        } else {
            if (thumb) { thumb.src = ''; thumb.style.display = 'none'; }
            if (ph) ph.style.display = 'flex';
            if (label) label.textContent = 'Upload Logo';
            if (sub) sub.textContent = 'PNG, JPG · optional · replaces color icon';
            if (removeBtn) removeBtn.classList.add('hidden');
        }
    }

    // ══════════════════════════════════════════════════════════════
    // RENDER GRID
    // ══════════════════════════════════════════════════════════════
    function renderGrid() {
        var html = paymentMethods.map(function(m) {
            var hasLogo  = !!m.logo_path;
            var staged   = editStagedLogo[m.id];
            var dispLogo = (staged === undefined) ? (m.logo_path ? '/' + m.logo_path : null) : (staged === 'REMOVE' ? null : staged);
            var showLogo = !!dispLogo;

            return '<div class="method-card ' + (m.is_active ? 'enabled' : 'disabled') + '" id="mcard-' + m.id + '">' +
                '<div class="method-card-top">' +
                    (showLogo
                        ? '<div class="method-icon" id="icon-' + m.id + '"><img src="' + escAttr(dispLogo) + '" alt="' + escAttr(m.name) + '"></div>'
                        : '<div class="method-icon" id="icon-' + m.id + '" style="background:' + m.bg_color + '">' + escHtml(m.icon_label || '') + '</div>'
                    ) +
                    '<div class="method-info">' +
                        '<div class="method-name" style="color:' + m.bg_color + '">' + escHtml(m.name) + '</div>' +
                        '<div class="method-account" id="disp-acct-' + m.id + '">' + escHtml(m.account_number) + '</div>' +
                        '<div class="method-acctname" id="disp-acctname-' + m.id + '">Account Name: ' + escHtml(m.account_name) + '</div>' +
                    '</div>' +
                    '<div class="card-actions">' +
                        '<div class="toggle-wrap">' +
                            '<label class="toggle-switch">' +
                                '<input type="checkbox" ' + (m.is_active ? 'checked' : '') + ' onchange="toggleMethod(\'' + m.id + '\', this.checked)">' +
                                '<span class="slider"></span>' +
                            '</label>' +
                            '<span class="status-badge ' + (m.is_active ? 'badge-enabled' : 'badge-disabled') + '" id="badge-' + m.id + '">' +
                                (m.is_active ? 'Active' : 'Inactive') +
                            '</span>' +
                        '</div>' +
                        '<button class="delete-btn-small" onclick="promptDelete(\'' + m.id + '\')" title="Delete this payment method">' +
                            '<svg width="13" height="13" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>' +
                        '</button>' +
                    '</div>' +
                '</div>' +

                '<div class="method-card-body" id="body-' + m.id + '">' +
                    '<div class="locked-banner" id="locked-banner-' + m.id + '" onclick="unlockEdit(\'' + m.id + '\')">' +
                        '<div style="display:flex;align-items:center;gap:8px;">' +
                            '<svg width="14" height="14" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24" id="lock-icon-' + m.id + '"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>' +
                            '<span class="locked-banner-text" id="lock-label-' + m.id + '">Click to Edit Details</span>' +
                        '</div>' +
                    '</div>' +

                    '<div class="edit-form" id="edit-form-' + m.id + '">' +
                        '<div class="edit-header">' +
                            '<span class="edit-header-label">Editing — ' + escHtml(m.name) + '</span>' +
                            '<button class="cancel-edit-btn" onclick="lockEdit(\'' + m.id + '\')">Cancel</button>' +
                        '</div>' +
                        '<div class="field-row">' +
                            '<div class="field-label">Account Number</div>' +
                            '<input class="field-input" id="inp-acct-' + m.id + '" value="' + escAttr(m.account_number) + '">' +
                        '</div>' +
                        '<div class="field-row">' +
                            '<div class="field-label">Account Name</div>' +
                            '<input class="field-input" id="inp-name-' + m.id + '" value="' + escAttr(m.account_name) + '">' +
                        '</div>' +

                        '<div class="field-row" style="margin-top:6px;">' +
                            '<div class="field-label">Logo / Icon Image <span style="color:#9ca3af;font-size:9px;">— optional</span></div>' +
                            '<div class="edit-img-zone" id="edit-img-zone-' + m.id + '">' +
                                '<input type="file" accept="image/*" id="edit-logo-input-' + m.id + '" onchange="handleEditLogoChange(\'' + m.id + '\', this)">' +
                                '<div class="edit-img-placeholder" id="edit-logo-ph-' + m.id + '" style="' + (hasLogo ? 'display:none;' : 'display:flex;') + '">...</div>' +
                                '<img class="edit-img-thumb" id="edit-logo-thumb-' + m.id + '" src="' + (hasLogo ? '/' + m.logo_path : '') + '" style="' + (hasLogo ? 'display:block;' : 'display:none;') + '">' +
                                '<div class="edit-img-info">' +
                                    '<p class="edit-img-label" id="edit-logo-label-' + m.id + '">' + (hasLogo ? 'Change Logo' : 'Upload Logo') + '</p>' +
                                    '<p class="edit-img-sub" id="edit-logo-sub-' + m.id + '">' + (hasLogo ? 'Current logo · Click to replace' : 'PNG, JPG') + '</p>' +
                                '</div>' +
                                '<button class="edit-img-remove ' + (hasLogo ? '' : 'hidden') + '" id="edit-logo-remove-' + m.id + '" onclick="removeEditLogo(\'' + m.id + '\', event)">Remove</button>' +
                            '</div>' +
                        '</div>' +
                        '<button class="save-btn" onclick="saveMethod(\'' + m.id + '\')">Save Changes</button>' +
                    '</div>' +
                '</div>' +
            '</div>';
        }).join('');

        // FIXED: Added the fully styled btn-add here so it always renders
        html += '<div style="margin-top: 16px;">' +
            '<button class="btn-add" onclick="openAddModal()">' +
                '<svg style="display:inline; width:14px; height:14px; margin-right:6px; vertical-align:-3px;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>' +
                'Add New Method' +
            '</button>' +
        '</div>';

        document.getElementById('methods-grid').innerHTML = html;
    }

    // Lock/Unlock handlers
    function unlockEdit(id) {
        document.getElementById('locked-banner-' + id).style.display = 'none';
        document.getElementById('edit-form-' + id).classList.add('visible');
        document.getElementById('body-' + id).classList.add('unlocked');
        delete editStagedLogo[id];
    }

    function lockEdit(id) {
        var m = paymentMethods.find(x => x.id == id);
        document.getElementById('inp-acct-' + id).value = m.account_number;
        document.getElementById('inp-name-' + id).value = m.account_name;
        delete editStagedLogo[id];
        var inp = document.getElementById('edit-logo-input-' + id);
        if (inp) inp.value = '';
        refreshEditLogoZone(id);
        document.getElementById('edit-form-' + id).classList.remove('visible');
        document.getElementById('locked-banner-' + id).style.display = '';
        document.getElementById('body-' + id).classList.remove('unlocked');
    }

    // ══════════════════════════════════════════════════════════════
    // API ACTIONS: TOGGLE, ADD, EDIT, DELETE
    // ══════════════════════════════════════════════════════════════

    // TOGGLE STATUS
    async function toggleMethod(id, enabled) {
        var m = paymentMethods.find(x => x.id == id);

        // Optimistic UI update
        var card = document.getElementById('mcard-' + id);
        card.classList.toggle('enabled', enabled);
        card.classList.toggle('disabled', !enabled);
        var badge = document.getElementById('badge-' + id);
        badge.className = 'status-badge ' + (enabled ? 'badge-enabled' : 'badge-disabled');
        badge.textContent = enabled ? 'Active' : 'Inactive';

        // Prepare full payload as required by the controller
        var fd = new FormData();
        fd.append('name', m.name);
        fd.append('icon_label', m.icon_label || '');
        fd.append('account_number', m.account_number);
        fd.append('account_name', m.account_name);
        fd.append('bg_color', m.bg_color);
        fd.append('is_active', enabled);

        try {
            let res = await fetch('/admin/payment-methods/' + id, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': getCsrfToken() },
                body: fd
            });
            let result = await res.json();
            if (result.success) {
                m.is_active = enabled;
                showToast(m.name + (enabled ? ' enabled ✓' : ' disabled'));
            } else {
                fetchMethods(); // Revert on failure
            }
        } catch (e) {
            fetchMethods(); // Revert on failure
        }
    }

    // ADD
    function updateAddPreview() {
        var nameVal = document.getElementById('add-name').value.trim() || 'New Method';
        var iconVal = document.getElementById('add-icon').value.trim() || '?';
        document.getElementById('add-name-preview').textContent = nameVal;
        if (!addStagedLogo) {
            document.getElementById('add-icon-text-preview').textContent = iconVal;
        }
    }

    function openAddModal() {
        ['add-name','add-icon','add-acct','add-acctname'].forEach(id => document.getElementById(id).value = '');
        document.querySelector('input[name="add-status"][value="true"]').checked = true;
        selectedColor = COLOR_PALETTE[0].hex;
        buildAddColorSwatches();
        document.getElementById('add-icon-preview').style.background = selectedColor;
        document.getElementById('add-icon-text-preview').textContent = '?';
        document.getElementById('add-name-preview').textContent = 'New Method';
        removeAddLogo(new Event('click'));
        openModal('add-modal');
    }

    async function confirmAdd() {
        var nameVal     = document.getElementById('add-name').value.trim();
        var iconVal     = document.getElementById('add-icon').value.trim();
        var acctVal     = document.getElementById('add-acct').value.trim();
        var acctNameVal = document.getElementById('add-acctname').value.trim();
        var statusVal   = document.querySelector('input[name="add-status"]:checked').value === 'true';

        if (!nameVal || !acctVal || !acctNameVal) {
            showToast('Please fill in all required fields.'); return;
        }

        var btn = document.querySelector('#add-modal .btn-primary');
        btn.disabled = true; btn.innerHTML = 'Adding...';

        var fd = new FormData();
        fd.append('name', nameVal);
        fd.append('icon_label', iconVal.toUpperCase());
        fd.append('account_number', acctVal);
        fd.append('account_name', acctNameVal);
        fd.append('bg_color', selectedColor);
        fd.append('is_active', statusVal);

        var fileInp = document.getElementById('add-logo-input');
        if (fileInp.files.length > 0) fd.append('logo', fileInp.files[0]);

        try {
            let res = await fetch('/admin/payment-methods', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': getCsrfToken() },
                body: fd
            });
            let result = await res.json();
            if (result.success) {
                closeModal('add-modal');
                showToast(nameVal + ' added successfully ✓');
                fetchMethods(); // Refresh grid
            } else {
                showToast('Validation error.');
            }
        } catch (e) {
            showToast('Server error occurred.');
        } finally {
            btn.disabled = false; btn.innerHTML = 'Add Method';
        }
    }

    // EDIT
    function saveMethod(id) {
        var m = paymentMethods.find(x => x.id == id);
        var newAcct = document.getElementById('inp-acct-' + id).value.trim();
        var newName = document.getElementById('inp-name-' + id).value.trim();
        var staged  = editStagedLogo[id];

        if (!newAcct || !newName) { showToast('Please fill in all fields.'); return; }

        var logoChanged = staged !== undefined;
        var newLogoVal  = logoChanged ? (staged === 'REMOVE' ? null : staged) : (m.logo_path ? '/' + m.logo_path : null);

        if ((newAcct === m.account_number) && (newName === m.account_name) && !logoChanged) {
            showToast('No changes made.'); lockEdit(id); return;
        }

        pendingSaveId   = id;
        pendingSaveAcct = newAcct;
        pendingSaveName = newName;
        pendingSaveLogo = newLogoVal;

        document.getElementById('modal-method-name').textContent  = m.name;
        document.getElementById('modal-preview-acct').textContent = newAcct;
        document.getElementById('modal-preview-name').textContent = newName;

        var logoRow = document.getElementById('modal-logo-row');
        if (logoChanged) {
            logoRow.style.display = '';
            if (newLogoVal) {
                document.getElementById('modal-preview-logo').src = newLogoVal;
                document.getElementById('modal-preview-logo').style.display = '';
                if(document.getElementById('modal-logo-removed-txt')) document.getElementById('modal-logo-removed-txt').style.display = 'none';
            } else {
                document.getElementById('modal-preview-logo').style.display = 'none';
                if (!document.getElementById('modal-logo-removed-txt')) {
                    var rt = document.createElement('p');
                    rt.id = 'modal-logo-removed-txt';
                    rt.style.cssText = 'font-size:11px;font-weight:700;color:#9ca3af;';
                    rt.textContent = 'Logo will be removed';
                    logoRow.appendChild(rt);
                } else {
                    document.getElementById('modal-logo-removed-txt').style.display = '';
                }
            }
        } else {
            logoRow.style.display = 'none';
        }

        openModal('confirm-modal');
    }

    async function confirmSave() {
        if (!pendingSaveId) return;
        var m = paymentMethods.find(x => x.id == pendingSaveId);

        var btn = document.querySelector('#confirm-modal .btn-primary');
        btn.disabled = true; btn.innerHTML = 'Saving...';

        var fd = new FormData();
        fd.append('name', m.name);
        fd.append('icon_label', m.icon_label || '');
        fd.append('bg_color', m.bg_color);
        fd.append('is_active', m.is_active);
        fd.append('account_number', pendingSaveAcct);
        fd.append('account_name', pendingSaveName);

        var fileInp = document.getElementById('edit-logo-input-' + pendingSaveId);
        if (fileInp && fileInp.files.length > 0) {
            fd.append('logo', fileInp.files[0]);
        }
        if (editStagedLogo[pendingSaveId] === 'REMOVE') {
            fd.append('remove_logo', 'true'); // Optional backend handler trigger
        }

        try {
            let res = await fetch('/admin/payment-methods/' + pendingSaveId, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': getCsrfToken() },
                body: fd
            });
            let result = await res.json();
            if (result.success) {
                closeModal('confirm-modal');
                showToast(m.name + ' details saved ✓');
                fetchMethods(); // Refresh grid
            }
        } catch (e) {
            showToast('Error saving changes');
        } finally {
            btn.disabled = false; btn.innerHTML = 'Confirm Save';
            pendingSaveId = null;
        }
    }

    // DELETE
    function promptDelete(id) {
        var m = paymentMethods.find(x => x.id == id);
        pendingDeleteId = id;
        document.getElementById('delete-method-name').textContent = m.name;
        openModal('delete-modal');
    }

    async function confirmDelete() {
        if (!pendingDeleteId) return;
        var btn = document.querySelector('#delete-modal .btn-danger');
        btn.disabled = true; btn.innerHTML = 'Deleting...';

        try {
            let res = await fetch('/admin/payment-methods/' + pendingDeleteId, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': getCsrfToken() }
            });
            let result = await res.json();
            if (result.success) {
                closeModal('delete-modal');
                showToast('Method removed ✓');
                fetchMethods(); // Refresh grid
            }
        } catch (e) {
            showToast('Error deleting method');
        } finally {
            btn.disabled = false; btn.innerHTML = 'Delete';
            pendingDeleteId = null;
        }
    }

    // ══════════════════════════════════════════════════════════════
    // MODALS & HELPERS
    // ══════════════════════════════════════════════════════════════
    function openModal(id) { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }

    document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
        backdrop.addEventListener('click', function(e) {
            if (e.target === backdrop) closeModal(backdrop.id);
        });
    });

    var toastTimer;
    function showToast(msg) {
        var t = document.getElementById('toast');
        if(t) {
            t.textContent = msg;
            t.classList.add('show');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(function(){ t.classList.remove('show'); }, 2800);
        }
    }

    function escHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
    function escAttr(s) { return escHtml(s); }

    // INITIALIZE ON LOAD
    document.addEventListener('DOMContentLoaded', fetchMethods);

    document.addEventListener('DOMContentLoaded', () => {
        const isFirstLogin = @json(auth()->user()->is_first_login);
        const userId = @json(auth()->id());
        const storageKey = 'berc_tutorial_step_' + userId;

        // If they are no longer on their first login, wipe memory and abort
        if (!isFirstLogin) {
            localStorage.removeItem(storageKey);
            return;
        }

        const tourState = localStorage.getItem(storageKey);

        // Trigger ONLY if they arrived from the History page
        if (tourState === 'sec_payment') {

            if (typeof window.driver === 'undefined') {
                console.error("Driver.js is missing on the Payment Settings page!");
                return;
            }

            const driver = window.driver.js.driver;
            const tour = driver({
                showProgress: true,
                allowClose: false,
                overlayColor: 'rgba(33, 60, 113, 0.75)',
                nextBtnText: 'Next &rarr;',
                prevBtnText: '&larr; Back',

                onDestroyStarted: () => {
                    if (!tour.hasNextStep()) {
                        // Final step: Move them to the Settings page to force password change!
                        localStorage.setItem(storageKey, 'sec_settings');
                        tour.destroy();
                        window.location.href = "{{ route('settings') }}"; // Make sure this matches your actual settings route name
                    } else {
                        tour.destroy();
                    }
                },

                steps: [
                    {
                        element: '#tour-payment-info',
                        popover: {
                            title: 'Managing Payments',
                            description: 'As a Secretary, you have full control over the payment methods displayed to researchers when they submit an application.',
                            side: "bottom",
                            align: 'start'
                        }
                    },
                    {
                        element: '#tour-payment-grid',
                        popover: {
                            title: 'The Payment Grid',
                            description: 'Here you can Add new banks or e-wallets, Edit existing account numbers, or temporarily Toggle a method offline if a bank is undergoing maintenance.',
                            side: "top",
                            align: 'start'
                        }
                    },
                    {
                        // Floating popover for the grand finale
                        popover: {
                            title: 'Final Step: Account Security 🔒',
                            description: 'You have completed the system tour! Because you are using a default, auto-generated password, your final requirement is to update it. Click below to proceed to your Account Settings.',
                            side: "bottom",
                            align: 'center',
                            doneBtnText: 'Update Password →'
                        }
                    }
                ]
            });

            tour.drive();
        }
    });
</script>

<script>
function startManualTutorial() {
    if (typeof window.driver === 'undefined') {
        console.error("Driver.js is missing on the Payment Settings page!");
        return;
    }

    const driver = window.driver.js.driver;

    const tour = driver({
        showProgress: true,
        allowClose: true,
        overlayColor: 'rgba(33, 60, 113, 0.75)',
        nextBtnText: 'Next →',
        prevBtnText: '← Back',

        onDestroyStarted: () => {
            tour.destroy();
        },

        steps: [
            {
                element: '#tour-payment-info',
                popover: {
                    title: 'Managing Payments',
                    description: 'As a Secretary, you have full control over the payment methods displayed to researchers when they submit an application.',
                    side: "bottom",
                    align: 'start'
                }
            },
            {
                element: '#tour-payment-grid',
                popover: {
                    title: 'The Payment Grid',
                    description: 'Here you can add new banks or e-wallets, edit existing account numbers, or temporarily toggle a method offline if a bank is undergoing maintenance.',
                    side: "top",
                    align: 'start'
                }
            },
            {
                popover: {
                    title: 'Tutorial Complete 🎉',
                    description: 'You have finished the Payment Settings tutorial.',
                    side: "bottom",
                    align: 'center',
                    doneBtnText: 'Finish'
                }
            }
        ]
    });

    tour.drive();
}
</script>

</body>
</html>
