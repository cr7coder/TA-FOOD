<!DOCTYPE html>
<html>

<head>
    <!-- Basic -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(Auth::check())
        <meta name="user-id" content="{{ Auth::user()->MaNguoiDung }}">
        <meta name="user-name" content="{{ Auth::user()->HoTen }}">
        <meta name="user-role" content="{{ Auth::user()->VaiTro }}">
    @endif
    <title>TAFOOD</title>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css"
        integrity="PASTE_THE_EXACT_HASH_FROM_CDNJS_HERE" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <!-- Local Font Awesome (fallback) -->
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link href="{{ asset('css/style.css') }}?v={{ time() }}" rel="stylesheet" />
    <link href="{{ asset('css/responsive.css') }}?v={{ time() }}" rel="stylesheet" />
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        .header_section {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background-color: transparent;
            transition: .3s
        }

        .header_section.scrolled {
            background-color: rgba(34, 40, 49, .95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1)
        }

        .header-address-input {
            display: none;
            opacity: 0;
            transition: .3s;
            margin-left: 5px;
        }

        .header-address-input.show {
            display: flex;
            opacity: 1;
        }

        /* Glassmorphic Unified Address & Search Inputs */
        .address-box-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 30px;
            padding: 2px 8px 2px 12px;
            width: 260px;
            height: 38px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-left: 15px;
        }

        .address-box-wrapper:focus-within {
            background: rgba(255, 255, 255, 0.98) !important;
            border-color: #ffbe33;
            box-shadow: 0 0 15px rgba(255, 190, 51, 0.4);
        }

        .address-input {
            border: none !important;
            background: transparent !important;
            color: #ffffff !important;
            font-size: 13px !important;
            width: 100%;
            outline: none !important;
            padding: 0 !important;
            height: 100% !important;
            box-shadow: none !important;
        }

        .address-box-wrapper:focus-within .address-input {
            color: #1e293b !important;
        }

        .address-input::placeholder {
            color: rgba(255, 255, 255, 0.6) !important;
        }

        .address-box-wrapper:focus-within .address-input::placeholder {
            color: #94a3b8 !important;
        }

        .location-btn {
            border: none !important;
            background: transparent !important;
            color: rgba(255, 255, 255, 0.7) !important;
            padding: 0 4px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            outline: none !important;
        }

        .address-box-wrapper:focus-within .location-btn {
            color: #ffbe33 !important;
        }

        .location-btn:hover {
            transform: scale(1.15);
        }

        .search-box-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            padding: 2px 4px 2px 14px;
            width: 270px;
            height: 38px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .search-box-wrapper:focus-within {
            background: rgba(255, 255, 255, 0.98) !important;
            border-color: rgba(0, 0, 0, 0.1) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05) !important;
        }

        .search-input {
            border: none !important;
            background: transparent !important;
            color: #ffffff !important;
            font-size: 13.5px !important;
            width: 100%;
            outline: none !important;
            padding: 0 !important;
            height: 100% !important;
            box-shadow: none !important;
        }

        .search-box-wrapper:focus-within .search-input {
            color: #1e293b !important;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.6) !important;
        }

        .search-box-wrapper:focus-within .search-input::placeholder {
            color: #94a3b8 !important;
        }

        .search-submit-btn {
            border: none !important;
            background: transparent !important;
            color: rgba(255, 255, 255, 0.9) !important;
            width: 30px;
            height: 30px;
            display: flex !important;
            align-items: center;
            justify-content: center;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            outline: none !important;
            padding: 0 !important;
        }

        .search-submit-btn i {
            font-size: 14px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1 !important;
        }

        .search-submit-btn:hover {
            transform: scale(1.15);
            color: #ffbe33 !important;
        }

        .search-box-wrapper:focus-within .search-submit-btn {
            background: transparent !important;
            color: #ff9800 !important;
        }

        /* Center search form in header relative to container */
        .custom_nav-container {
            position: relative !important;
        }

        @media (min-width: 992px) {
            #searchForm {
                position: absolute !important;
                left: 50% !important;
                top: 50% !important;
                transform: translate(-50%, -50%) !important;
                margin: 0 !important;
                z-index: 10;
            }
        }

        .hero_area {
            padding-top: 80px
        }

        /* Custom Notification Bell & Dropdown CSS */
        .no-caret::after {
            display: none !important;
        }
        
        .notification-dropdown {
            display: flex;
            align-items: center;
            margin-right: 15px;
        }

        /* Beautiful upward pointing arrow for the dropdown menu */
        .notification-dropdown .dropdown-menu::before {
            content: '';
            position: absolute;
            top: -6px;
            right: 22px;
            width: 12px;
            height: 12px;
            background: #222831; /* Matches the dark brand header color */
            transform: rotate(45deg);
            z-index: 1;
        }

        .notification-dropdown .dropdown-menu {
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            background: #222831 !important; /* Set to dark to match the header and prevent subpixel leakage */
            backdrop-filter: blur(20px) !important;
            border-radius: 16px !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15), 0 5px 15px rgba(0, 0, 0, 0.05) !important;
            transform: translateY(12px);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: visible !important; /* Allow the arrow ::before to show */
            animation: dropdownSlideIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .notification-list {
            background: #f8fafc !important;
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
            padding: 8px 0 !important;
        }

        @keyframes dropdownSlideIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(12px);
            }
        }

        #notificationBadge {
            font-size: 9px;
            padding: 3px 6px;
            font-weight: 700;
            border: 1.5px solid #222831;
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.6);
            animation: badge-pulse 1.8s infinite;
        }

        @keyframes badge-pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }

        /* Modern thin scrollbar styling */
        .notification-list::-webkit-scrollbar {
            width: 6px;
        }
        .notification-list::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.02);
            border-radius: 10px;
        }
        .notification-list::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.12);
            border-radius: 10px;
            transition: background 0.2s;
        }
        .notification-list::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 190, 51, 0.6);
        }

        /* Bell Ringing Keyframes */
        @keyframes bellRing {
            0% { transform: rotate(0); }
            10% { transform: rotate(15deg); }
            20% { transform: rotate(-10deg); }
            30% { transform: rotate(10deg); }
            40% { transform: rotate(-8deg); }
            50% { transform: rotate(6deg); }
            60% { transform: rotate(-4deg); }
            70% { transform: rotate(3deg); }
            80% { transform: rotate(-2deg); }
            90% { transform: rotate(1deg); }
            100% { transform: rotate(0); }
        }

        .animate-ring {
            display: inline-block;
            transform-origin: top center;
        }

        .notification-dropdown:hover .animate-ring,
        #notificationBell:hover .fa-bell {
            animation: bellRing 1s ease;
        }

        .notification-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            margin: 8px 12px;
            background-color: #ffffff !important;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            border-left: 4px solid #e2e8f0;
            transition: all 0.25s ease;
            text-decoration: none !important;
            cursor: pointer;
            position: relative;
        }

        .notification-item:hover {
            background-color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 190, 51, 0.15);
            border-left-color: #ffbe33;
        }

        .notification-item.unread {
            background-color: #ffffff !important;
            border-left: 4px solid #ffbe33;
            box-shadow: 0 4px 14px rgba(255, 190, 51, 0.1);
        }

        .notification-item-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            box-shadow: 0 3px 8px rgba(0,0,0,0.03);
            transition: transform 0.2s ease;
        }

        .notification-item-icon i {
            line-height: 1;
            margin: 0;
        }

        .notification-item:hover .notification-item-icon {
            transform: scale(1.06);
        }

        /* Modern soft colors for statuses (iOS style) */
        .bg-status-pending { background: rgba(255, 193, 7, 0.12) !important; color: #d97706 !important; }
        .bg-status-confirmed { background: rgba(59, 130, 246, 0.12) !important; color: #1d4ed8 !important; }
        .bg-status-processing { background: rgba(6, 182, 212, 0.12) !important; color: #0891b2 !important; }
        .bg-status-delivering { background: rgba(139, 92, 246, 0.12) !important; color: #6d28d9 !important; }
        .bg-status-completed { background: rgba(16, 185, 129, 0.12) !important; color: #047857 !important; }
        .bg-status-cancelled { background: rgba(239, 68, 68, 0.12) !important; color: #b91c1c !important; }

        /* Typography styling for notification text */
        .notification-item-title {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: color 0.2s;
        }

        .notification-item.unread .notification-item-title {
            font-weight: 700;
            color: #1e293b;
        }

        .notification-item-desc {
            font-size: 11.5px;
            color: #64748b;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
        }

        .notification-item.unread .notification-item-desc {
            color: #334155;
        }

        .notification-item-time {
            font-size: 10.5px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 4px;
        }

        .unread-dot {
            width: 8px;
            height: 8px;
            background-color: #ffbe33;
            border-radius: 50%;
            flex-shrink: 0;
            margin-left: 8px;
            align-self: center;
            box-shadow: 0 0 6px #ffbe33;
            animation: dot-pulse 1.5s infinite;
            transition: all 0.25s ease;
        }

        @keyframes dot-pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 190, 51, 0.7); }
            70% { transform: scale(1.25); box-shadow: 0 0 0 4px rgba(255, 190, 51, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 190, 51, 0); }
        }

        /* Toast notifications */
        .toast-card {
            width: 320px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.16);
            border-left: 5px solid #ffbe33;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            opacity: 0;
            transform: translateX(350px);
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
        }

        .toast-card.show {
            opacity: 1;
            transform: translateX(0);
        }

        .toast-card.hide {
            opacity: 0;
            transform: translateX(350px);
        }
        /* ==========================================
           AI CULINARY ASSISTANT (CHATBOX) STYLING
           ========================================== */
        
        /* Floating Chat Bubble */
        .ai-chat-bubble {
            position: fixed;
            bottom: 110px; /* Cách cart một khoảng 20px (30 + 60 + 20) */
            right: 30px;
            width: 60px !important;
            height: 60px !important;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffbe33, #e69d00);
            border: none;
            color: #222831;
            font-size: 26px; /* Kích thước icon */
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1060;
            box-shadow: 0 4px 15px rgba(255, 190, 51, 0.4);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            outline: none !important;
            padding: 0 !important;
            box-sizing: border-box !important;
        }

        .ai-chat-bubble:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(255, 190, 51, 0.6);
        }

        .ai-chat-bubble .bubble-glow {
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            border-radius: 50%;
            border: 2px solid rgba(255, 190, 51, 0.8);
            animation: pulse-glow 2s infinite;
            pointer-events: none;
        }

        .ai-chat-bubble .pulse-dot {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 10px;
            height: 10px;
            background: #10b981;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        @keyframes pulse-glow {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.3); opacity: 0; }
        }

        @media (max-width: 991px) {
            .ai-chat-bubble {
                bottom: 100px !important; /* Mobile: Cách cart một khoảng (25 + 54 + 21 = 100) */
                right: 20px !important;
                width: 54px !important;
                height: 54px !important;
                font-size: 22px !important;
            }
        }

        /* Glassmorphic Chat Panel */
        .ai-chat-panel {
            position: fixed;
            bottom: 170px;
            right: 30px;
            z-index: 1060; /* Đè lên các thành phần như sticky-top ở checkout */
            width: 380px;
            height: 550px;
            border-radius: 20px;
            background: rgba(34, 40, 49, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            animation: slideUpIn 0.3s ease;
        }

        @keyframes slideUpIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @media (max-width: 576px) {
            .ai-chat-panel {
                bottom: 155px;
                right: 15px;
                left: 15px;
                width: calc(100% - 30px);
                height: calc(100% - 240px);
                max-height: 500px;
            }
        }

        /* Header Styling */
        .ai-chat-header {
            padding: 15px 20px;
            background: rgba(255, 190, 51, 0.08);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ai-header-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ai-avatar-wrapper {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #ffbe33;
            color: #222831;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            position: relative;
        }

        .status-indicator {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid #222831;
        }

        .status-indicator.online {
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
        }

        .ai-title {
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            margin: 0;
            line-height: 1.2;
        }

        .ai-subtitle {
            color: #94a3b8;
            font-size: 11px;
        }

        .close-ai-chat {
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 24px;
            cursor: pointer;
            transition: color 0.2s;
            outline: none !important;
            padding: 0;
            line-height: 1;
        }

        .close-ai-chat:hover {
            color: #ffffff;
        }

        /* Message Log Area */
        .ai-chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            scroll-behavior: smooth;
        }

        .ai-chat-messages::-webkit-scrollbar {
            width: 4px;
        }

        .ai-chat-messages::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
        }

        /* Message Bubbles */
        .message {
            display: flex;
            width: 100%;
            animation: fadeInBubble 0.25s ease;
        }

        @keyframes fadeInBubble {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .ai-message {
            justify-content: flex-start;
        }

        .user-message {
            justify-content: flex-end;
        }

        .message-bubble {
            max-width: 80%;
            padding: 10px 14px;
            border-radius: 16px;
            font-size: 13.5px;
            line-height: 1.5;
            word-break: break-word;
        }

        .ai-message .message-bubble {
            background: rgba(255, 255, 255, 0.08);
            color: #f1f5f9;
            border-bottom-left-radius: 4px;
            border: 1px solid rgba(255, 255, 255, 0.03);
        }

        .user-message .message-bubble {
            background: #ffbe33;
            color: #222831;
            border-bottom-right-radius: 4px;
            font-weight: 500;
        }

        /* Typing indicator animation */
        .typing-indicator {
            display: flex;
            gap: 4px;
            padding: 12px 16px;
            align-items: center;
        }

        .typing-indicator span {
            width: 6px;
            height: 6px;
            background: #cbd5e1;
            border-radius: 50%;
            animation: bounceTyping 1.4s infinite ease-in-out both;
        }

        .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
        .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }

        @keyframes bounceTyping {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        /* Suggestion Chips */
        .ai-chat-suggestions {
            padding: 8px 15px;
            display: flex;
            gap: 8px;
            overflow-x: auto;
            white-space: nowrap;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(0, 0, 0, 0.1);
            -webkit-overflow-scrolling: touch; /* Momentum scrolling on mobile */
        }

        .ai-chat-suggestions::-webkit-scrollbar {
            height: 3px;
        }

        .ai-chat-suggestions::-webkit-scrollbar-thumb {
            background: rgba(255, 190, 51, 0.4);
            border-radius: 10px;
        }

        .ai-chat-suggestions::-webkit-scrollbar-track {
            background: transparent;
        }

        .ai-chip {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 11.5px;
            cursor: pointer;
            transition: all 0.2s ease;
            outline: none !important;
            flex-shrink: 0 !important; /* Prevent buttons from shrinking to enable horizontal scrolling */
        }

        .ai-chip:hover {
            background: rgba(255, 190, 51, 0.15);
            border-color: #ffbe33;
            color: #ffbe33;
        }

        /* Input Area styling */
        .ai-chat-input-area {
            padding: 12px 15px;
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ai-chat-input-area input {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 30px;
            padding: 10px 18px;
            color: #ffffff;
            font-size: 13.5px;
            outline: none;
            transition: border-color 0.2s;
        }

        .ai-chat-input-area input:focus {
            border-color: #ffbe33;
            background: rgba(255, 255, 255, 0.08);
        }

        .ai-send-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #ffbe33;
            border: none;
            color: #222831;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s, background-color 0.2s;
            outline: none !important;
        }

        .ai-send-btn:hover {
            background: #e69d00;
            transform: scale(1.05);
        }

        /* Interactive Food Recommendation Cards inside Chat */
        .ai-foods-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
            margin-top: 8px;
        }

        .ai-food-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 10px;
            display: flex;
            gap: 12px;
            align-items: center;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .ai-food-card:hover {
            border-color: rgba(255, 190, 51, 0.5);
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-1px);
        }

        .ai-food-img {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
            background: rgba(0, 0, 0, 0.2);
        }

        .ai-food-info {
            flex: 1;
            min-width: 0;
        }

        .ai-food-name {
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ai-food-res {
            font-size: 11px;
            color: #94a3b8;
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: color 0.2s;
        }

        .ai-food-res:hover {
            color: #ffbe33 !important;
            text-decoration: underline;
        }

        .ai-food-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 5px;
        }

        .ai-food-price {
            font-size: 13px;
            font-weight: 700;
            color: #ffbe33;
        }

        .ai-food-add-btn {
            background: #ffbe33;
            color: #222831;
            border: none;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
            outline: none !important;
        }

        .ai-food-add-btn:hover {
            background: #e69d00;
            transform: translateY(-1px);
        }

        .ai-food-add-btn:active {
            transform: translateY(0);
        }

        /* Global User Dropdown & Avatar Polish */
        .user-dropdown {
            padding: 8px 15px !important;
            border-radius: 25px !important;
            background: rgba(255, 255, 255, 0.1) !important;
            transition: all 0.3s ease !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .user-dropdown:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            text-decoration: none !important;
            color: white !important;
        }

        .user-avatar {
            width: 32px !important;
            height: 32px !important;
            border-radius: 50% !important;
            background: #ffbe33 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            color: #333 !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12) !important;
        }

        .dropdown-menu {
            border-radius: 16px !important;
            border: 1px solid rgba(0, 0, 0, 0.06) !important;
            background: rgba(255, 255, 255, 0.94) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08), 0 5px 15px rgba(0, 0, 0, 0.04) !important;
            min-width: 270px !important;
            padding: 8px !important;
            overflow: hidden !important;
            animation: dropdownFadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .dropdown-header {
            padding: 16px 20px !important;
            background: linear-gradient(135deg, rgba(255, 107, 53, 0.03) 0%, rgba(247, 147, 30, 0.03) 100%) !important;
            border-radius: 10px !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
            margin-bottom: 6px !important;
        }

        .dropdown-header strong {
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #1e293b !important;
            display: block !important;
            margin-bottom: 2px !important;
        }

        .dropdown-header small {
            font-size: 12.5px !important;
            color: #64748b !important;
        }

        .dropdown-item {
            padding: 12px 16px !important;
            font-size: 14.5px !important;
            font-weight: 600 !important;
            color: #475569 !important;
            border-radius: 8px !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            background: transparent !important;
        }

        .dropdown-item:hover,
        .dropdown-item.active {
            background: #222831 !important;
            color: #ffffff !important;
            transform: translateX(6px) !important;
        }

        .dropdown-item i {
            width: 20px !important;
            font-size: 16px !important;
            text-align: center !important;
            color: #64748b !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            margin-right: 0 !important;
        }

        .dropdown-item:hover i,
        .dropdown-item.active i {
            color: #ffbe33 !important;
            transform: scale(1.15) !important;
        }

        .dropdown-divider {
            margin: 6px 0 !important;
            border-top: 1px solid rgba(0, 0, 0, 0.05) !important;
        }

        /* Floating Cart Styles */
        .floating-cart {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1060; /* Above Bootstrap modal backdrop and content */
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .cart-icon {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            position: relative;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
            animation: pulse 2s infinite;
        }

        .cart-icon:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.6);
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            border: 2px solid white;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
            }
            50% {
                box-shadow: 0 4px 25px rgba(255, 107, 53, 0.8);
            }
            100% {
                box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
            }
        }

        .cart-modal {
            display: none;
            position: fixed;
            z-index: 1070; /* Above bootstrap modal stack */
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .cart-modal-content {
            position: absolute;
            right: 20px;
            top: 20px;
            display: flex;
            flex-direction: column;
            width: 400px;
            max-height: calc(100vh - 40px);
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .cart-header {
            flex: 0 0 auto;
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .close-cart {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }

        .close-cart:hover {
            transform: scale(1.2);
        }

        .cart-body {
            flex: 1 1 auto;
            overflow-y: auto;
            padding: 20px;
            max-height: none !important;
        }

        .empty-cart {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-cart i {
            margin-bottom: 15px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-image {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-name {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .cart-item-price {
            color: #ff6b35;
            font-weight: 600;
        }

        .cart-item-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-btn {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .quantity-btn:hover {
            background: #e9ecef;
        }

        .quantity {
            min-width: 30px;
            text-align: center;
            font-weight: 600;
        }

        .remove-item {
            color: #dc3545;
            cursor: pointer;
            font-size: 18px;
            padding: 5px;
        }

        .remove-item:hover {
            color: #c82333;
        }

        .cart-footer {
            flex: 0 0 auto;
            padding: 20px;
            border-top: 1px solid #eee;
            background: #f8f9fa;
        }

        .cart-total {
            text-align: center;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .cart-actions {
            display: flex;
            gap: 10px;
        }

        .cart-actions button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary {
            background: white;
            color: #6c757d;
            border: 1px solid #6c757d;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
        }

        .btn-warning {
            background: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background: #e0a800;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .cart-modal-content {
                width: 95%;
                right: 2.5%;
                left: auto;
                top: 20px;
                max-height: calc(100vh - 40px);
            }

            .floating-cart {
                bottom: 20px;
                right: 20px;
            }

            .cart-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
        }

        /* 
         * Lực lượng đặc nhiệm: Ép vị trí Chatbot và Cart bằng ID trên mobile 
         * Để vượt qua mọi rào cản CSS cache và override
         */
        @media (max-width: 768px) {
            #floatingCart {
                bottom: 25px !important;
                right: 20px !important;
                width: 54px !important;
                height: 54px !important;
            }
            #floatingCart .cart-icon {
                width: 100% !important;
                height: 100% !important;
                font-size: 22px !important;
            }
            #aiChatBubble {
                bottom: 95px !important;
                right: 20px !important; 
                width: 54px !important;
                height: 54px !important;
                font-size: 22px !important;
            }
            #aiChatPanel {
                bottom: 160px !important;
                right: 15px !important;
                max-height: 65vh !important;
            }
        }

        /* Suggestions list dropdown styling */
        .address-suggestions-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(34, 40, 49, 0.98);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            z-index: 9999;
            max-height: 250px;
            overflow-y: auto;
            margin-top: 5px;
            display: none;
            padding: 5px 0;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        .address-suggestion-item {
            padding: 10px 15px;
            color: #ffffff;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .address-suggestion-item:last-child {
            border-bottom: none;
        }

        .address-suggestion-item:hover {
            background: rgba(255, 190, 51, 0.2);
            color: #ffbe33;
        }

        .address-suggestion-item i {
            color: #ffbe33;
            font-size: 14px;
            flex-shrink: 0;
        }
    </style>
</head>

<body>
    <div id="liveToastContainer" class="position-fixed" style="top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px;"></div>

    <!-- Floating Cart Button -->
    <div id="floatingCart" class="floating-cart">
        <div class="cart-icon">
            <i class="fa fa-shopping-cart"></i>
            <span class="cart-count" id="cartCount">0</span>
        </div>
    </div>

    <!-- Cart Popup Modal -->
    <div id="cartModal" class="cart-modal">
        <div class="cart-modal-content">
            <div class="cart-header">
                <h3><i class="fa fa-shopping-cart"></i> Giỏ hàng của bạn</h3>
                <span class="close-cart" id="closeCart">&times;</span>
            </div>
            <div class="cart-body" id="cartBody">
                <div class="empty-cart" id="emptyCart">
                    <i class="fa fa-shopping-cart fa-3x text-muted"></i>
                    <p>Giỏ hàng trống</p>
                    <small>Hãy thêm món ăn vào giỏ hàng!</small>
                </div>
                <div class="cart-items" id="cartItems" style="display: none;">
                    <!-- Cart items sẽ được load bằng JavaScript -->
                </div>
            </div>
            <div class="cart-footer" id="cartFooter" style="display: none;">
                <div class="cart-total">
                    <strong>Tổng cộng: <span id="cartTotal">0</span> đ</strong>
                </div>
                <div class="cart-actions">
                    <button class="btn btn-outline-secondary" onclick="clearCart()">
                        <i class="fa fa-trash"></i> Xóa tất cả
                    </button>
                    <button class="btn btn-warning" onclick="goToCheckout()">
                        <i class="fa fa-credit-card"></i> Thanh toán
                    </button>
                </div>
            </div>
        </div>
    </div>

    @yield('content')

    <!-- footer section -->
    <footer class="footer_section">
        <div class="container">
            <div class="row">
                <div class="col-md-4 footer-col">
                    <div class="footer_contact">
                        <h4>Liên hệ với chúng tôi</h4>
                        <div class="contact_link_box">
                            <a href=""><i class="fa fa-map-marker" aria-hidden="true"></i><span>Địa chỉ: 175 Tây Sơn,
                                    Đống Đa, Hà Nội</span></a>
                            <a href=""><i class="fa fa-phone" aria-hidden="true"></i><span>Điện thoại +84
                                    123456789</span></a>
                            <a href=""><i class="fa fa-envelope"
                                    aria-hidden="true"></i><span>tafoods@gmail.com</span></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 footer-col">
                    <div class="footer_detail">
                        <a href="" class="footer-logo">TAFOOD</a>
                        <p>Trung tâm hỗ trợ</p>
                        <p>Câu hỏi thường gặp</p>
                        <p>Điều khoản và điều kiện</p>
                        <div class="footer_social">
                            <a href=""><i class="fa fa-facebook" aria-hidden="true"></i></a>
                            <a href=""><i class="fa fa-twitter" aria-hidden="true"></i></a>
                            <a href=""><i class="fa fa-linkedin" aria-hidden="true"></i></a>
                            <a href=""><i class="fa fa-instagram" aria-hidden="true"></i></a>
                            <a href=""><i class="fa fa-pinterest" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 footer-col">
                    <h4>Tải ứng dụng của chúng tôi</h4>
                    <img src="{{ asset('images/apple.png') }}" alt="">
                    <img src="{{ asset('images/android.png') }}" alt="">
                </div>
            </div>
            <div class="footer-info">
                <p>&copy; <span id="displayYear"></span> All Rights Reserved By <a
                        href="https://html.design/">TAFOOD</a><br><br>
                    &copy; <span id="displayYear"></span> Distributed By <a href="https://themewagon.com/"
                        target="_blank">Nguyễn Tuấn Anh</a></p>
            </div>
        </div>
    </footer>

    <!-- Global Screen-Centered Glassmorphic Alert System -->
    @include('client.partials._global-alert')

    <!-- JS: ĐÚNG THỨ TỰ - CHỈ MỘT BẢN jQuery/Bootstrap -->
    <!-- 1) jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <!-- 2) Bootstrap bundle (kèm Popper) - dùng 1 bản -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- 3) jQuery plugins phụ thuộc vào jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js"></script>

    <!-- 4) Script của bạn (đã thêm guard để không lỗi khi plugin thiếu) -->
    <script src="{{ asset('js/custom.js') }}"></script>

    <!-- 5) Google Maps: định nghĩa callback TRƯỚC khi nạp script -->
    <script>
        window.myMap = function () {
            var el = document.getElementById('googleMap');
            if (!el || !window.google || !google.maps) return;
            var center = { lat: 21.028511, lng: 105.804817 }; // Hà Nội mặc định
            var map = new google.maps.Map(el, {
                center: center,
                zoom: 13,
                mapTypeControl: false,
                streetViewControl: false
            });
            new google.maps.Marker({ position: center, map: map });
        };
    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCh39n5U-4IoWpsVGUHWdqB6puEkhRLdmI&callback=myMap"></script>

    <!-- Các script khác (Leaflet, custom inline) -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const headerInput = document.getElementById("headerAddressInput");
            const mainInput = document.getElementById("addressInput");
            const mainLocation = document.getElementById("mainLocation");
            const headerSection = document.querySelector(".header_section");
            const headerAddressInput = document.querySelector(".header-address-input");
            const GOONG_API_KEY = @json(config('services.goong.api_key'));

            async function fetchAddress(lat, lng) {
                if (GOONG_API_KEY && GOONG_API_KEY.trim() !== '' && !GOONG_API_KEY.includes('GOONG_API_KEY')) {
                    try {
                        const response = await fetch(`https://rsapi.goong.io/Geocode?latlng=${lat},${lng}&api_key=${GOONG_API_KEY}`);
                        const data = await response.json();
                        if (data && data.results && data.results.length > 0) {
                            return data.results[0].formatted_address;
                        }
                    } catch (e) {
                        console.warn("Goong reverse geocoding failed, falling back to Nominatim: ", e);
                    }
                }
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=vi`);
                    const data = await response.json();
                    return data.display_name || `${lat}, ${lng}`;
                } catch (e) {
                    alert("Không thể lấy địa chỉ từ API!");
                    return `${lat}, ${lng}`;
                }
            }

            let miniMapInstance = null;
            let miniMapMarker = null;
            let lastGeocodedAddress = "";

            function showAddressDetails(address, lat = null, lng = null, debounceMs = 1200) {
                const panel = document.getElementById("addressDetailPanel");
                const gpsText = document.getElementById("gpsCoords");
                if (!panel) return;
                
                if (address && address.trim().length > 3) {
                    // Reset animation classes
                    panel.classList.remove("d-none", "animate__fadeOut");
                    panel.classList.add("animate__fadeIn");

                    if (lat && lng) {
                        gpsText.textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                        lastGeocodedAddress = address;
                        initOrUpdateMiniMap(lat, lng, address);
                    } else {
                        // Only geocode if the input text has actually changed to prevent duplicate geocodes and marker jumps
                        if (address !== lastGeocodedAddress) {
                            gpsText.textContent = "Đang tra cứu tọa độ...";
                            geocodeAddress(address, debounceMs);
                        }
                    }
                    
                    // Reset all tag buttons first
                    document.querySelectorAll(".address-tag-btn").forEach(b => {
                        b.style.background = "rgba(255,255,255,0.1)";
                        b.style.color = "white";
                        b.style.borderColor = "rgba(255,255,255,0.4)";
                    });
                    
                    // Save to local storage
                    localStorage.setItem("ta_food_delivery_address", address);
                    
                    // Highlight existing tag if matches
                    const savedTag = localStorage.getItem("ta_food_delivery_tag");
                    if (savedTag) {
                        const matchedBtn = Array.from(document.querySelectorAll(".address-tag-btn")).find(btn => btn.getAttribute("data-tag") === savedTag);
                        if (matchedBtn) {
                            matchedBtn.style.background = "#ffbe33";
                            matchedBtn.style.color = "#1e293b";
                            matchedBtn.style.borderColor = "#ffbe33";
                        }
                    }
                } else {
                    panel.classList.add("d-none");
                }
            }

            // Debounced geocoding search to avoid spamming Nominatim API
            let geocodeTimeout = null;
            function geocodeAddress(address, debounceMs = 1200) {
                clearTimeout(geocodeTimeout);
                geocodeTimeout = setTimeout(async () => {
                    const gpsText = document.getElementById("gpsCoords");
                    
                    if (GOONG_API_KEY && GOONG_API_KEY.trim() !== '' && !GOONG_API_KEY.includes('GOONG_API_KEY')) {
                        try {
                            const response = await fetch(`https://rsapi.goong.io/geocode?address=${encodeURIComponent(address)}&api_key=${GOONG_API_KEY}`);
                            const data = await response.json();
                            if (data && data.results && data.results.length > 0) {
                                const result = data.results[0];
                                const lat = parseFloat(result.geometry.location.lat);
                                const lng = parseFloat(result.geometry.location.lng);
                                if (gpsText) gpsText.textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                                
                                lastGeocodedAddress = address;
                                initOrUpdateMiniMap(lat, lng, address);
                                
                                localStorage.setItem("ta_food_delivery_lat", lat);
                                localStorage.setItem("ta_food_delivery_lng", lng);
                                localStorage.setItem("ta_food_gps_authorized", "true");
                                return;
                            }
                        } catch (e) {
                            console.warn("Goong geocoding failed, falling back to Nominatim: ", e);
                        }
                    }

                    async function queryNominatim(q, delayMs = 0) {
                        if (delayMs > 0) {
                            await new Promise(resolve => setTimeout(resolve, delayMs));
                        }
                        try {
                            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=1&countrycodes=vn`);
                            if (!response.ok) return null;
                            const data = await response.json();
                            return (data && data.length > 0) ? data[0] : null;
                        } catch (e) {
                            return null;
                        }
                    }

                    function stripAddressPrefix(q) {
                        return q.replace(/^(thôn|xóm|tổ|số nhà|số|ngõ|ngách|hẻm|đường|phố|ấp|khu)\s+\S+/i, '').trim();
                    }

                    try {
                        let currentQuery = address.trim();
                        let result = await queryNominatim(currentQuery);

                        // Fallback 1: If full search yields nothing and has commas, strip the most specific part and query the rest
                        if (!result && currentQuery.includes(',')) {
                            const parts = currentQuery.split(',');
                            if (parts.length > 1) {
                                const firstPartCleaned = stripAddressPrefix(parts[0].trim());
                                const rest = parts.slice(1).map(p => p.trim()).join(', ');
                                const fallbackQuery = (firstPartCleaned ? firstPartCleaned + ', ' : '') + rest;
                                // Add 1000ms delay to respect Nominatim API rate limits
                                result = await queryNominatim(fallbackQuery, 1000);
                            }
                        }

                        // Fallback 2: Try stripping the first 1-2 words (prefixes like "Thôn Trong", "Xóm 3")
                        if (!result) {
                            const cleanedQuery = stripAddressPrefix(currentQuery);
                            if (cleanedQuery && cleanedQuery !== currentQuery) {
                                // Add 1000ms delay to respect Nominatim API rate limits
                                result = await queryNominatim(cleanedQuery, 1000);
                            }
                        }

                        // Fallback 3: Fallback directly to the last 2 words (usually the province/city)
                        if (!result) {
                            const words = currentQuery.split(/\s+/);
                            if (words.length > 2) {
                                const fallbackQuery = words.slice(words.length - 2).join(' ').trim();
                                // Add 1000ms delay to respect Nominatim API rate limits
                                result = await queryNominatim(fallbackQuery, 1000);
                            }
                        }

                        if (result) {
                            const lat = parseFloat(result.lat);
                            const lng = parseFloat(result.lon);
                            if (gpsText) gpsText.textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                            
                            lastGeocodedAddress = address;
                            
                            // Map visual updates
                            initOrUpdateMiniMap(lat, lng, address);
                            
                            localStorage.setItem("ta_food_delivery_lat", lat);
                            localStorage.setItem("ta_food_delivery_lng", lng);
                            localStorage.setItem("ta_food_gps_authorized", "true");
                        } else {
                            if (gpsText) gpsText.textContent = "Định vị mạng di động";
                        }
                    } catch (e) {
                        console.warn("Geocoding failed: " + e.message);
                    }
                }, debounceMs);
            }

            // Setup Autocomplete Suggestions Dropdown
            function setupAutocomplete(inputElement) {
                if (!inputElement) return;

                let suggestionsBox = document.createElement("div");
                suggestionsBox.className = "address-suggestions-list";
                inputElement.parentNode.appendChild(suggestionsBox);

                let autocompleteTimeout = null;

                document.addEventListener("click", function(e) {
                    if (e.target !== inputElement && e.target !== suggestionsBox && !suggestionsBox.contains(e.target)) {
                        suggestionsBox.style.display = "none";
                    }
                });

                inputElement.addEventListener("input", function() {
                    const query = this.value.trim();
                    clearTimeout(autocompleteTimeout);

                    if (query.length < 3) {
                        suggestionsBox.style.display = "none";
                        suggestionsBox.innerHTML = "";
                        return;
                    }

                    autocompleteTimeout = setTimeout(async () => {
                        try {
                            if (GOONG_API_KEY && GOONG_API_KEY.trim() !== '' && !GOONG_API_KEY.includes('GOONG_API_KEY')) {
                                const res = await fetch(`https://rsapi.goong.io/Place/AutoComplete?api_key=${GOONG_API_KEY}&input=${encodeURIComponent(query)}`);
                                const data = await res.json();
                                // Check if input value matches query to avoid race conditions
                                if (inputElement.value.trim() !== query) return;
                                if (data && data.predictions) {
                                    renderGoongSuggestions(data.predictions, suggestionsBox, inputElement);
                                }
                            } else {
                                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=vn&accept-language=vi`);
                                const data = await res.json();
                                // Check if input value matches query to avoid race conditions
                                if (inputElement.value.trim() !== query) return;
                                if (data) {
                                    renderNominatimSuggestions(data, suggestionsBox, inputElement);
                                }
                            }
                        } catch (err) {
                            console.warn("Autocomplete error:", err);
                        }
                    }, 350); // Lowered to 350ms for snappier suggestions
                });

                inputElement.addEventListener("focus", function() {
                    if (this.value.trim().length >= 3 && suggestionsBox.children.length > 0) {
                        suggestionsBox.style.display = "block";
                    }
                });
            }

            function renderNominatimSuggestions(results, box, input) {
                box.innerHTML = "";
                if (results.length === 0) {
                    box.style.display = "none";
                    return;
                }

                results.forEach(item => {
                    const div = document.createElement("div");
                    div.className = "address-suggestion-item";
                    div.innerHTML = `<i class="fa fa-map-marker-alt"></i> <span>${item.display_name}</span>`;
                    
                    div.addEventListener("click", function() {
                        const address = item.display_name;
                        input.value = address;
                        box.style.display = "none";

                        const lat = parseFloat(item.lat);
                        const lng = parseFloat(item.lon);

                        if (input === headerInput && mainInput) {
                            mainInput.value = address;
                        }

                        showAddressDetails(address, lat, lng);
                    });
                    box.appendChild(div);
                });
                box.style.display = "block";
            }

            async function renderGoongSuggestions(predictions, box, input) {
                box.innerHTML = "";
                if (predictions.length === 0) {
                    box.style.display = "none";
                    return;
                }

                predictions.forEach(item => {
                    const div = document.createElement("div");
                    div.className = "address-suggestion-item";
                    div.innerHTML = `<i class="fa fa-map-marker-alt"></i> <span>${item.description}</span>`;
                    
                    div.addEventListener("click", async function() {
                        const address = item.description;
                        input.value = address;
                        box.style.display = "none";

                        if (input === headerInput && mainInput) {
                            mainInput.value = address;
                        }

                        try {
                            const detailRes = await fetch(`https://rsapi.goong.io/Place/Detail?api_key=${GOONG_API_KEY}&place_id=${item.place_id}`);
                            const detailData = await detailRes.json();
                            if (detailData && detailData.result && detailData.result.geometry) {
                                const lat = detailData.result.geometry.location.lat;
                                const lng = detailData.result.geometry.location.lng;
                                showAddressDetails(address, lat, lng);
                            }
                        } catch (err) {
                            console.error("Error fetching Goong place details:", err);
                        }
                    });
                    box.appendChild(div);
                });
                box.style.display = "block";
            }

            setupAutocomplete(mainInput);
            setupAutocomplete(headerInput);

            function initOrUpdateMiniMap(lat, lng, address) {
                const mapDiv = document.getElementById("miniMap");
                if (!mapDiv) return;
                
                // Initialize map if not already done
                if (!miniMapInstance) {
                    miniMapInstance = L.map('miniMap', {
                        center: [lat, lng],
                        zoom: 15,
                        zoomControl: false // Keep it ultra clean
                    });
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(miniMapInstance);
                    
                    // Create beautiful custom orange pin marker
                    miniMapMarker = L.marker([lat, lng], { draggable: true }).addTo(miniMapInstance);
                    
                    // Update address inputs when marker is dragged!
                    miniMapMarker.on('dragend', async function(event) {
                        const marker = event.target;
                        const position = marker.getLatLng();
                        const newLat = position.lat;
                        const newLng = position.lng;
                        
                        const gpsText = document.getElementById("gpsCoords");
                        if (gpsText) gpsText.textContent = `${newLat.toFixed(5)}, ${newLng.toFixed(5)}`;
                        
                        localStorage.setItem("ta_food_delivery_lat", newLat);
                        localStorage.setItem("ta_food_delivery_lng", newLng);
                        localStorage.setItem("ta_food_gps_authorized", "true");
                        
                        // Show temporary loading popup
                        marker.bindPopup(`<strong style="color: #ff9800;">📍 Đang xác định địa chỉ...</strong>`).openPopup();
                        
                        const newAddress = await fetchAddress(newLat, newLng);
                        lastGeocodedAddress = newAddress;
                        if (headerInput) headerInput.value = newAddress;
                        if (mainInput) mainInput.value = newAddress;
                        localStorage.setItem("ta_food_delivery_address", newAddress);
                        
                        marker.bindPopup(`<strong style="color: #ff9800;">📍 Vị trí ghim mới:</strong><br>${newAddress}`).openPopup();
                    });

                    // Allow user to click directly on the map to place the marker!
                    miniMapInstance.on('click', async function(event) {
                        const newLat = event.latlng.lat;
                        const newLng = event.latlng.lng;
                        
                        miniMapMarker.setLatLng([newLat, newLng]);
                        
                        const gpsText = document.getElementById("gpsCoords");
                        if (gpsText) gpsText.textContent = `${newLat.toFixed(5)}, ${newLng.toFixed(5)}`;
                        
                        localStorage.setItem("ta_food_delivery_lat", newLat);
                        localStorage.setItem("ta_food_delivery_lng", newLng);
                        localStorage.setItem("ta_food_gps_authorized", "true");
                        
                        // Show temporary loading popup
                        miniMapMarker.bindPopup(`<strong style="color: #ff9800;">📍 Đang xác định địa chỉ...</strong>`).openPopup();
                        
                        const newAddress = await fetchAddress(newLat, newLng);
                        lastGeocodedAddress = newAddress;
                        if (headerInput) headerInput.value = newAddress;
                        if (mainInput) mainInput.value = newAddress;
                        localStorage.setItem("ta_food_delivery_address", newAddress);
                        
                        miniMapMarker.bindPopup(`<strong style="color: #ff9800;">📍 Vị trí ghim mới:</strong><br>${newAddress}`).openPopup();
                    });
                } else {
                    // Update existing map & marker coordinates
                    miniMapInstance.setView([lat, lng], 15);
                    miniMapMarker.setLatLng([lat, lng]);
                }
                
                miniMapMarker.bindPopup(`<strong style="color: #ffbe33; font-weight: 700;">📍 Vị trí giao món:</strong><br>${address}`).openPopup();
                
                // Fix Leaflet container size inside absolute/hidden parents
                setTimeout(() => {
                    miniMapInstance.invalidateSize();
                }, 100);
            }

            function getLocationAndFillInputs() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(async (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        const address = await fetchAddress(lat, lng);
                        if (headerInput) headerInput.value = address;
                        if (mainInput) {
                            mainInput.value = address;
                            showAddressDetails(address, lat, lng);
                        }
                        localStorage.setItem("ta_food_delivery_lat", lat);
                        localStorage.setItem("ta_food_delivery_lng", lng);
                        localStorage.setItem("ta_food_gps_authorized", "true");
                    }, (error) => {
                        console.warn("Không thể lấy vị trí Geolocation: " + error.message);
                        // Chỉ thông báo thân thiện nếu ô nhập địa chỉ đang trống rỗng
                        if ((!mainInput || !mainInput.value.trim()) && (!headerInput || !headerInput.value.trim())) {
                            alert("Không thể tự động định vị vị trí của bạn. Vui lòng nhập địa chỉ thủ công!");
                        }
                    });
                } else {
                    console.warn("Trình duyệt không hỗ trợ định vị!");
                }
            }

            function syncInputs(input1, input2) {
                if (!input1 || !input2) return;
                input1.addEventListener("input", () => {
                    input2.value = input1.value;
                });
                input2.addEventListener("input", () => {
                    input1.value = input2.value;
                });
            }
            if (headerInput && mainInput) syncInputs(headerInput, mainInput);

            window.addEventListener("scroll", () => {
                if (window.scrollY > 200) {
                    headerAddressInput?.classList.add("show");
                    headerSection?.classList.add("scrolled");
                    if (mainLocation) mainLocation.style.display = "none";
                } else {
                    headerAddressInput?.classList.remove("show");
                    headerSection?.classList.remove("scrolled");
                    if (mainLocation) mainLocation.style.display = "block";
                }
            });

            // Listen to manual typing in mainInput and headerInput with smart timing
            if (mainInput) {
                mainInput.addEventListener("change", function() {
                    showAddressDetails(this.value, null, null, 50);
                });
                mainInput.addEventListener("keydown", function(e) {
                    if (e.key === "Enter") {
                        e.preventDefault();
                        showAddressDetails(this.value, null, null, 50);
                    }
                });
                // Show detail panel on load if address is already typed in/filled
                if (mainInput.value.trim()) {
                    showAddressDetails(mainInput.value, null, null, 50);
                }

                // Click search button handler
                const syncAddressBtn = document.getElementById("syncAddressBtn");
                if (syncAddressBtn) {
                    syncAddressBtn.addEventListener("click", function() {
                        const val = mainInput.value.trim();
                        if (val) {
                            showAddressDetails(val, null, null, 50);
                        } else {
                            mainInput.focus();
                        }
                    });
                }
            }
            if (headerInput) {
                headerInput.addEventListener("change", function() {
                    if (mainInput) mainInput.value = this.value;
                    showAddressDetails(this.value, null, null, 50);
                });
                headerInput.addEventListener("keydown", function(e) {
                    if (e.key === "Enter") {
                        e.preventDefault();
                        if (mainInput) mainInput.value = this.value;
                        showAddressDetails(this.value, null, null, 50);
                    }
                });
            }
            
            // Listen to address tags selection
            document.querySelectorAll(".address-tag-btn").forEach(btn => {
                btn.addEventListener("click", async function(e) {
                    e.preventDefault();
                    
                    const currentAddress = mainInput ? mainInput.value : "";
                    if (!currentAddress.trim()) {
                        alert("Vui lòng nhập hoặc ghim địa chỉ trước khi lưu!");
                        return;
                    }

                    const isAuthenticated = @json(Auth::check());
                    if (!isAuthenticated) {
                        if (window.showGlobalAlert) {
                            window.showGlobalAlert("Yêu cầu đăng nhập", "Vui lòng đăng nhập để lưu địa chỉ của bạn!", "info");
                        } else {
                            alert("Vui lòng đăng nhập để lưu địa chỉ của bạn!");
                        }
                        return;
                    }

                    const tag = this.getAttribute("data-tag");

                    // Xác nhận lưu địa chỉ bằng popup confirm glassmorphic
                    if (window.showConfirm) {
                        const confirmed = await window.showConfirm(
                            "Xác nhận lưu địa chỉ?",
                            `Bạn có chắc chắn muốn lưu địa chỉ này làm <strong>${tag}</strong> không?<br><span style="font-size: 12.5px; color: #cbd5e1; display: block; margin-top: 8px; word-break: break-all; opacity: 0.85;">${currentAddress}</span>`
                        );
                        if (!confirmed) return;
                    }
                    
                    // Reset all tags style
                    document.querySelectorAll(".address-tag-btn").forEach(b => {
                        b.style.background = "rgba(255,255,255,0.1)";
                        b.style.color = "white";
                        b.style.borderColor = "rgba(255,255,255,0.4)";
                    });
                    
                    // Highlight selected
                    this.style.background = "#ffbe33";
                    this.style.color = "#1e293b";
                    this.style.borderColor = "#ffbe33";
                    
                    if (currentAddress.trim()) {
                        localStorage.setItem("ta_food_delivery_address", currentAddress);
                        localStorage.setItem("ta_food_delivery_tag", tag);
                        localStorage.setItem("ta_food_gps_authorized", "true");

                        // Map tag label to API key
                        const tagKeyMap = {
                            "Nhà riêng": "nha_rieng",
                            "Văn phòng": "van_phong",
                            "Trường học": "truong_hoc"
                        };
                        const tagKey = tagKeyMap[tag];

                        // Save to DB if logged in
                        const isAuthenticated = @json(Auth::check());
                        if (isAuthenticated && tagKey) {
                            try {
                                const resp = await fetch('{{ route("profile.update-tag-address") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({ tag: tagKey, address: currentAddress })
                                });
                                const result = await resp.json();
                                const panel = document.getElementById("addressDetailPanel");
                                const notice = document.createElement("div");
                                notice.className = "text-warning font-weight-bold mt-2 animate__animated animate__pulse save-tag-notice";
                                notice.style.fontSize = "12px";
                                notice.innerHTML = result.success
                                    ? `✔️ Đã lưu địa chỉ làm <strong>${tag}</strong> thành công!`
                                    : `⚠️ Lưu thất bại: ${result.message || "Lỗi không xác định"}`;
                                const existingNotice = panel.querySelector(".save-tag-notice");
                                if (existingNotice) existingNotice.remove();
                                panel.appendChild(notice);
                                setTimeout(() => notice.remove(), 2500);
                            } catch (err) {
                                console.error("Lỗi lưu địa chỉ:", err);
                            }
                        } else {
                            // Guest: just show notice (localStorage already saved above)
                            const panel = document.getElementById("addressDetailPanel");
                            const notice = document.createElement("div");
                            notice.className = "text-warning font-weight-bold mt-2 animate__animated animate__pulse save-tag-notice";
                            notice.style.fontSize = "12px";
                            notice.innerHTML = `✔️ Đã lưu địa chỉ làm <strong>${tag}</strong> (Đăng nhập để lưu vĩnh viễn)`;
                            const existingNotice = panel.querySelector(".save-tag-notice");
                            if (existingNotice) existingNotice.remove();
                            panel.appendChild(notice);
                            setTimeout(() => notice.remove(), 2500);
                        }
                    }
                });
            });

            // Listen to default address setting
            document.querySelectorAll(".save-default-address-btn").forEach(btn => {
                btn.addEventListener("click", async function(e) {
                    e.preventDefault();
                    
                    const currentAddress = mainInput ? mainInput.value : "";
                    if (!currentAddress.trim()) {
                        alert("Vui lòng nhập hoặc ghim địa chỉ trước khi đặt làm mặc định!");
                        return;
                    }

                    const isAuthenticated = @json(Auth::check());
                    if (!isAuthenticated) {
                        if (window.showGlobalAlert) {
                            window.showGlobalAlert("Yêu cầu đăng nhập", "Vui lòng đăng nhập để lưu địa chỉ mặc định cho tài khoản của bạn!", "info");
                        } else {
                            alert("Vui lòng đăng nhập để lưu địa chỉ mặc định cho tài khoản của bạn!");
                        }
                        return;
                    }

                    // Xác nhận đặt địa chỉ mặc định bằng popup confirm glassmorphic
                    if (window.showConfirm) {
                        const confirmed = await window.showConfirm(
                            "Đặt địa chỉ mặc định?",
                            `Bạn có chắc chắn muốn lưu địa chỉ này làm địa chỉ mặc định không?<br><span style="font-size: 12.5px; color: #cbd5e1; display: block; margin-top: 8px; word-break: break-all; opacity: 0.85;">${currentAddress}</span>`
                        );
                        if (!confirmed) return;
                    }

                    btn.disabled = true;
                    const oldHtml = btn.innerHTML;
                    btn.innerHTML = `<i class="fa fa-spinner fa-spin"></i> Đang lưu...`;

                    try {
                        const response = await fetch('{{ route("profile.update-address") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                DiaChi: currentAddress
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            const panel = document.getElementById("addressDetailPanel");
                            const notice = document.createElement("div");
                            notice.className = "text-success font-weight-bold mt-2 animate__animated animate__pulse save-tag-notice";
                            notice.style.fontSize = "12px";
                            notice.innerHTML = `⭐ ${data.message}`;
                            const existingNotice = panel.querySelector(".save-tag-notice");
                            if (existingNotice) existingNotice.remove();
                            panel.appendChild(notice);
                            setTimeout(() => notice.remove(), 3500);

                            // Clear all tag-specific saved addresses from localStorage
                            // so the checkout page no longer shows stale tag buttons
                            ["Nhà riêng", "Văn phòng", "Trường học"].forEach(tag => {
                                localStorage.removeItem("ta_food_address_" + tag);
                                localStorage.removeItem("ta_food_lat_" + tag);
                                localStorage.removeItem("ta_food_lng_" + tag);
                            });
                            // Also clear the current delivery tag since it's now the default
                            localStorage.removeItem("ta_food_delivery_tag");

                            // Reset address tag button styles on the homepage panel
                            document.querySelectorAll(".address-tag-btn").forEach(b => {
                                b.style.background = "rgba(255,255,255,0.1)";
                                b.style.color = "white";
                                b.style.borderColor = "rgba(255,255,255,0.4)";
                            });

                            if (window.showGlobalAlert) {
                                window.showGlobalAlert("Thành công", `Đã lưu "${currentAddress}" làm địa chỉ giao hàng mặc định của bạn!`, "success");
                            }

                            // Collapse address panel after 3 seconds so user sees success notice
                            setTimeout(() => {
                                if (panel && !panel.classList.contains("d-none")) {
                                    panel.classList.remove("animate__fadeIn");
                                    panel.classList.add("animate__fadeOut");
                                    setTimeout(() => panel.classList.add("d-none"), 400);
                                }
                            }, 3000);
                        } else {
                            alert("Có lỗi xảy ra: " + (data.message || "Không xác định"));
                        }
                    } catch (err) {
                        console.error(err);
                        alert("Lỗi kết nối. Vui lòng thử lại!");
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = oldHtml;
                    }
                });
            });

            document.getElementById("getLocationBtn")?.addEventListener("click", () => getLocationAndFillInputs());
            document.getElementById("headerGetLocationBtn")?.addEventListener("click", () => getLocationAndFillInputs());

            // Close only the detail sub-panel (GPS info, map, tags)
            document.getElementById("closeDetailPanel")?.addEventListener("click", function () {
                const panel = document.getElementById("addressDetailPanel");
                if (panel) {
                    panel.classList.remove("animate__fadeIn");
                    panel.classList.add("animate__fadeOut");
                    setTimeout(() => panel.classList.add("d-none"), 350);
                }
            });
        });
    </script>
    <script>
        @if(Auth::check())
        // Client Notification System
        document.addEventListener("DOMContentLoaded", function () {
            const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
            if (!userId) return;

            const badge = document.getElementById('notificationBadge');
            const container = document.getElementById('notificationsContainer');
            const emptyNotifications = document.getElementById('emptyNotifications');
            const markAllReadBtn = document.getElementById('markAllReadBtn');
            let knownNotifications = new Set();
            let isFirstLoad = true;

            function markItemAsRead(item, id) {
                if (!item || !item.classList.contains('unread')) return;

                item.classList.remove('unread');
                item.style.borderLeftColor = '#e2e8f0';

                // Fade out and remove the unread dot
                const dot = item.querySelector('.unread-dot');
                if (dot) {
                    dot.style.transform = 'scale(0)';
                    dot.style.opacity = '0';
                    setTimeout(() => dot.remove(), 250);
                }

                // Decrease badge count
                if (badge) {
                    let currentCount = parseInt(badge.textContent) || 0;
                    if (currentCount > 1) {
                        badge.textContent = currentCount - 1;
                    } else {
                        badge.classList.add('d-none');
                        badge.textContent = '0';
                    }
                }

                // Call API to mark as read
                fetch(`/api/v1/notifications/${id}/read`, {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    keepalive: true
                });
            }

            // Synthesize a crisp premium dual-tone chime sound natively with Web Audio API
            function playChimeSound() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    
                    const osc1 = ctx.createOscillator();
                    const gain1 = ctx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(880, ctx.currentTime); // A5
                    osc1.frequency.exponentialRampToValueAtTime(1320, ctx.currentTime + 0.15); // E6
                    gain1.gain.setValueAtTime(0.12, ctx.currentTime);
                    gain1.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
                    osc1.connect(gain1);
                    gain1.connect(ctx.destination);
                    osc1.start();
                    osc1.stop(ctx.currentTime + 0.5);
                    
                    const osc2 = ctx.createOscillator();
                    const gain2 = ctx.createGain();
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(1046.5, ctx.currentTime + 0.08); // C6
                    gain2.gain.setValueAtTime(0.08, ctx.currentTime + 0.08);
                    gain2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.6);
                    osc2.connect(gain2);
                    gain2.connect(ctx.destination);
                    osc2.start(ctx.currentTime + 0.08);
                    osc2.stop(ctx.currentTime + 0.6);
                } catch (e) {
                    console.log("Audio blocked/muted:", e);
                }
            }

            function getStatusColor(bgClass) {
                if (bgClass === 'bg-status-cancelled') return '#b91c1c';
                if (bgClass === 'bg-status-confirmed') return '#1d4ed8';
                if (bgClass === 'bg-status-processing') return '#0891b2';
                if (bgClass === 'bg-status-delivering') return '#6d28d9';
                if (bgClass === 'bg-status-completed') return '#047857';
                return '#d97706'; // pending
            }

            function showToast(title, message, orderId, targetUrl = null) {
                const toastContainer = document.getElementById('liveToastContainer');
                if (!toastContainer) return;

                const { icon, bg } = getStatusIcon(message);
                const borderLeftColor = getStatusColor(bg);

                const card = document.createElement('div');
                card.className = 'toast-card';
                card.style.borderLeftColor = borderLeftColor;
                card.innerHTML = `
                    <div class="notification-item-icon ${bg}">
                        <i class="fa ${icon}"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <strong style="display: block; font-size: 13px; color: #333; font-weight: 600;">${title}</strong>
                        <span style="font-size: 12px; color: #555; display: block; margin-top: 2px;">${message}</span>
                    </div>
                    <button type="button" class="close-toast-btn" style="background: none; border: none; font-size: 18px; color: #bbb; cursor: pointer; padding: 0; line-height: 1; transition: color 0.2s;" onmouseover="this.style.color='#666';" onmouseout="this.style.color='#bbb';">&times;</button>
                `;

                // Synthesize & play premium bell sound!
                playChimeSound();

                // Append and animate slide in
                toastContainer.appendChild(card);
                setTimeout(() => card.classList.add('show'), 100);

                // Clicking the toast goes to order detail or custom target URL
                card.addEventListener('click', function(e) {
                    if (e.target.classList.contains('close-toast-btn')) return;
                    if (targetUrl) {
                        window.location.href = targetUrl;
                    } else if (orderId) {
                        window.location.href = `/orders/${orderId}`;
                    }
                });

                // Auto hide
                const hideTimeout = setTimeout(() => {
                    card.classList.remove('show');
                    card.classList.add('hide');
                    setTimeout(() => card.remove(), 500);
                }, 6000);

                // Close button handler
                card.querySelector('.close-toast-btn').addEventListener('click', (e) => {
                    e.stopPropagation();
                    clearTimeout(hideTimeout);
                    card.classList.remove('show');
                    card.classList.add('hide');
                    setTimeout(() => card.remove(), 500);
                });
            }

            function getStatusIcon(message) {
                const msgLower = message.toLowerCase();
                if (msgLower.includes('hủy')) return { icon: 'fa-times', bg: 'bg-status-cancelled' };
                if (msgLower.includes('xác nhận')) return { icon: 'fa-check', bg: 'bg-status-confirmed' };
                if (msgLower.includes('chuần bị') || msgLower.includes('chuẩn bị')) return { icon: 'fa-hamburger', bg: 'bg-status-processing' };
                if (msgLower.includes('giao tới')) return { icon: 'fa-motorcycle', bg: 'bg-status-delivering' };
                if (msgLower.includes('thành công')) return { icon: 'fa-pizza-slice', bg: 'bg-status-completed' };
                return { icon: 'fa-bell', bg: 'bg-status-pending' };
            }

            function fetchNotifications() {
                fetch('/api/v1/notifications')
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            const { notifications, unread_count } = res.data;

                            // Badge Update
                            if (unread_count > 0) {
                                if (badge) {
                                    badge.textContent = unread_count;
                                    badge.classList.remove('d-none');
                                }
                            } else {
                                if (badge) badge.classList.add('d-none');
                            }

                            // Render Dropdown List
                            if (notifications.length > 0) {
                                if (emptyNotifications) emptyNotifications.style.display = 'none';
                                
                                let html = '';
                                notifications.forEach(n => {
                                    const { icon, bg } = getStatusIcon(n.message);
                                    const unreadClass = !n.is_read ? 'unread' : '';
                                    const orderUrl = n.target_url ? n.target_url : (n.order_id ? `/orders/${n.order_id}` : '#');
                                    const displayTitle = (n.title && n.title.trim()) ? n.title : 'Cập nhật đơn hàng';

                                    html += `
                                        <a href="${orderUrl}" class="notification-item ${unreadClass}" data-id="${n.id}">
                                            <div class="notification-item-icon ${bg}">
                                                <i class="fa ${icon}"></i>
                                            </div>
                                            <div style="flex: 1; min-width: 0;">
                                                <div class="notification-item-title">${displayTitle}</div>
                                                <div class="notification-item-desc">${n.message}</div>
                                                <div class="notification-item-time">
                                                    <i class="far fa-clock"></i>
                                                    <span>${n.time_diff}</span>
                                                </div>
                                            </div>
                                            ${!n.is_read ? `
                                                <div class="unread-dot"></div>
                                            ` : ''}
                                        </a>
                                    `;

                                    // Push dynamic real-time toast for *NEW* unread notifications
                                    if (!isFirstLoad && !n.is_read && !knownNotifications.has(n.id)) {
                                        showToast(n.title, n.message, n.order_id, n.target_url);
                                        
                                        // Tự động load lại dữ liệu màn hình nếu khách đang ở trang Đơn hàng
                                        if (window.location.pathname.includes('/orders')) {
                                            if (typeof loadOrders === 'function') {
                                                loadOrders();
                                            }
                                            if (typeof loadOrderDetail === 'function') {
                                                loadOrderDetail();
                                            }
                                        }
                                    }
                                    knownNotifications.add(n.id);
                                });
                                if (container) container.innerHTML = html;

                                // Setup IntersectionObserver for auto-reading on scroll/view
                                const listContainer = document.querySelector('.notification-list');
                                const observer = new IntersectionObserver((entries) => {
                                    entries.forEach(entry => {
                                        if (entry.isIntersecting) {
                                            const item = entry.target;
                                            const id = item.getAttribute('data-id');
                                            
                                            // Auto-read when user scrolls and sees this item (wait 500ms to verify user is actually reading/looking)
                                            if (item.classList.contains('unread')) {
                                                setTimeout(() => {
                                                    // Re-verify if item is still intersecting and unread
                                                    if (item.classList.contains('unread')) {
                                                        markItemAsRead(item, id);
                                                    }
                                                }, 500);
                                            }
                                            observer.unobserve(item);
                                        }
                                    });
                                }, {
                                    root: listContainer,
                                    threshold: 0.6 // 60% of the item must be visible
                                });

                                // Register event handlers for each notification item
                                document.querySelectorAll('.notification-item').forEach(item => {
                                    const id = item.getAttribute('data-id');
                                    
                                    // 1) Click handler (marks as read and navigates)
                                    item.addEventListener('click', function(e) {
                                        markItemAsRead(this, id);
                                    });

                                    // 2) Hover (mouseenter) handler for PC
                                    item.addEventListener('mouseenter', function() {
                                        if (this.classList.contains('unread')) {
                                            setTimeout(() => {
                                                if (this.matches(':hover') && this.classList.contains('unread')) {
                                                    markItemAsRead(this, id);
                                                }
                                            }, 500); // 500ms hover delay
                                        }
                                    });

                                    // 3) Observe scroll visibility
                                    if (item.classList.contains('unread')) {
                                        observer.observe(item);
                                    }
                                });

                            } else {
                                if (emptyNotifications) emptyNotifications.style.display = 'block';
                                if (container) container.innerHTML = '';
                            }

                            isFirstLoad = false;
                        }
                    })
                    .catch(err => console.error("Error fetching notifications:", err));
            }

            // Mark all as read
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Optimistic update
                    if (badge) {
                        badge.classList.add('d-none');
                        badge.textContent = '0';
                    }
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                        item.style.borderLeftColor = '#e2e8f0';
                        const dot = item.querySelector('.unread-dot');
                        if (dot) dot.remove();
                    });

                    fetch('/api/v1/notifications/read-all', {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        keepalive: true
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            fetchNotifications();
                        }
                    });
                });
            }

            // Initial fetch & set poll interval (10 seconds)
            fetchNotifications();
            setInterval(fetchNotifications, 10000);
        });
        @endif
    </script>

    <style>
        .ai-settings-btn {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            font-size: 16px;
            cursor: pointer;
            transition: color 0.2s, transform 0.2s;
            outline: none !important;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .ai-settings-btn:hover {
            color: #ffbe33;
            transform: scale(1.1);
        }
        
        .ai-settings-panel {
            position: absolute;
            top: 50px;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(20, 24, 33, 0.95);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
            z-index: 10;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            animation: slideInChatSettings 0.3s ease-out forwards;
        }
        
        @keyframes slideInChatSettings {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .settings-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 10px;
            color: #ffffff;
        }

        .settings-header h6 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .close-settings-btn {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            font-size: 20px;
            cursor: pointer;
            transition: color 0.2s;
            line-height: 1;
        }
        
        .close-settings-btn:hover {
            color: #ffffff;
        }

        .settings-body {
            color: #cbd5e1;
            font-size: 12px;
            line-height: 1.5;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .settings-body p {
            margin: 0;
        }

        .settings-body ol {
            padding-left: 18px;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .settings-input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 5px;
        }

        .settings-input-group input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 10px;
            color: #ffffff;
            font-size: 12px;
            outline: none;
            transition: border-color 0.2s;
        }

        .settings-input-group input:focus {
            border-color: #ffbe33;
        }

        .save-key-btn {
            background: #ffbe33;
            color: #222831;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
        }

        .save-key-btn:hover {
            background: #e69d00;
        }
        
        .save-key-btn:active {
            transform: scale(0.98);
        }

        .api-key-status {
            font-size: 11px;
            margin-top: 5px;
            text-align: center;
            font-weight: 600;
            min-height: 16px;
        }
        
        .api-key-status.success {
            color: #22c55e;
        }

        .api-key-status.error {
            color: #ef4444;
        }

        .ai-share-loc-btn {
            background: linear-gradient(135deg, #ffbe33, #e69d00);
            color: #222831 !important;
            border: none;
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(255, 190, 51, 0.3);
            transition: all 0.2s ease;
        }
        .ai-share-loc-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 190, 51, 0.4);
            filter: brightness(1.05);
        }
        .ai-share-loc-btn:active {
            transform: translateY(0);
        }

        .ai-loc-options {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .manual-address-form {
            display: flex;
            gap: 6px;
            margin-top: 10px;
            background: rgba(255, 255, 255, 0.1);
            padding: 5px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            align-items: center;
        }
        
        .manual-address-input {
            flex: 1;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 12px;
            padding: 4px 8px;
            outline: none;
            width: 100%;
        }
        
        .manual-address-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .manual-address-submit-btn {
            background: #ffbe33;
            color: #222831;
            border: none;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .manual-address-submit-btn:hover {
            background: #e69d00;
        }
    </style>

    <!-- AI Culinary Assistant (Chatbot) -->
    <button id="aiChatBubble" class="ai-chat-bubble" title="Trợ lý ẩm thực AI">
        <div class="bubble-glow"></div>
        <i class="fa fa-robot"></i>
        <span class="pulse-dot"></span>
    </button>

    <div id="aiChatPanel" class="ai-chat-panel d-none">
        <!-- Settings Panel -->
        <div id="aiSettingsPanel" class="ai-settings-panel d-none">
            <div class="settings-header">
                <h6><i class="fa fa-key" style="color: #ffbe33;"></i> Cấu hình TAFOOD AI</h6>
                <button id="closeAiSettings" class="close-settings-btn" title="Đóng">&times;</button>
            </div>
            <div class="settings-body">
                <p>Mở khóa Trợ lý AI siêu thông minh bằng cách tích hợp **Gemini API Key** miễn phí!</p>
                <ol>
                    <li>Truy cập <a href="https://aistudio.google.com/" target="_blank" style="color: #ffbe33; text-decoration: underline;">Google AI Studio</a></li>
                    <li>Đăng nhập và bấm **"Create API Key"**</li>
                    <li>Sao chép Key và dán vào ô bên dưới:</li>
                </ol>
                <div class="settings-input-group">
                    <input type="password" id="geminiApiKeyInput" placeholder="Dán Gemini API Key vào đây..." autocomplete="off">
                    <button id="saveApiKeyBtn" class="save-key-btn">Lưu cấu hình</button>
                </div>
                <div id="apiKeyStatus" class="api-key-status"></div>
            </div>
        </div>

        <div class="ai-chat-header">
            <div class="ai-header-info">
                <div class="ai-avatar-wrapper">
                    <i class="fa fa-robot"></i>
                    <span class="status-indicator online"></span>
                </div>
                <div>
                    <h5 class="ai-title">TAFOOD AI</h5>
                    <span class="ai-subtitle">Trợ lý Ẩm thực Trực tuyến</span>
                </div>
            </div>
            <div style="display: flex; gap: 8px; align-items: center;">
                <button id="openAiSettings" class="ai-settings-btn" title="Cấu hình API Key"><i class="fa fa-key"></i></button>
                <button id="closeAiChat" class="close-ai-chat" title="Đóng">&times;</button>
            </div>
        </div>

        <div id="aiChatMessages" class="ai-chat-messages">
            <div class="message ai-message">
                <div class="message-bubble">
                    Xin chào! Mình là **TAFOOD AI** 🤖. Hôm nay bạn thèm ăn gì thế? Mình có thể gợi ý những món ngon chuẩn vị nhất và săn mã giảm giá hot giúp bạn đó! 🍕🍔🥤
                </div>
            </div>
        </div>

        <div class="ai-chat-suggestions">
            <button class="ai-chip" data-text="Tìm cơm ngon trưa nay">🍛 Cơm ngon trưa nay</button>
            <button class="ai-chip" data-text="Món nước ấm nóng có nước lèo">🍜 Bún phở ấm nóng</button>
            <button class="ai-chip" data-text="Tìm nước uống giải nhiệt">🥤 Trà sữa & Nước ngọt</button>
            <button class="ai-chip" data-text="Có mã giảm giá voucher nào hot không?">🎁 Săn Voucher hot</button>
        </div>

        <form id="aiChatInputForm" class="ai-chat-input-area">
            <input type="text" id="aiChatInput" placeholder="Hỏi TAFOOD AI..." autocomplete="off">
            <button type="submit" class="ai-send-btn" title="Gửi">
                <i class="fa fa-paper-plane"></i>
            </button>
        </form>
    </div>

    <!-- AI Chatbot Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const chatBubble = document.getElementById("aiChatBubble");
            const chatPanel = document.getElementById("aiChatPanel");
            
            // Global helper to open restaurant menu modal across all pages (seamless navigation)
            window.showRestaurantMenuModal = function(id) {
                if (!id) return;
                
                // Dim/close the chat panel to give clear view of the popup menu
                if (chatPanel) {
                    chatPanel.classList.add("d-none");
                    sessionStorage.setItem('tafood_chat_open', 'false');
                }
                
                const targetButton = document.querySelector(`.view-restaurant-menu[data-id="${id}"]`);
                if (targetButton && typeof $ !== 'undefined' && $('#restaurantFoodsModal').length > 0) {
                    targetButton.click();
                } else {
                    // Redirect to home and trigger auto-open
                    window.location.href = `/?open_restaurant=${id}`;
                }
            };

            const closeChat = document.getElementById("closeAiChat");
            const chatForm = document.getElementById("aiChatInputForm");
            const chatInput = document.getElementById("aiChatInput");
            const messagesContainer = document.getElementById("aiChatMessages");
            const suggestionChips = document.querySelectorAll(".ai-chip");

            // API Key Settings Toggle & Action
            const openSettingsBtn = document.getElementById("openAiSettings");
            const closeSettingsBtn = document.getElementById("closeAiSettings");
            const settingsPanel = document.getElementById("aiSettingsPanel");
            const saveKeyBtn = document.getElementById("saveApiKeyBtn");
            const keyInput = document.getElementById("geminiApiKeyInput");
            const keyStatus = document.getElementById("apiKeyStatus");

            let hasGeminiKey = @json(!empty(session('GEMINI_API_KEY')) || !empty(env('GEMINI_API_KEY')));

            function updateKeyInputPlaceholder() {
                if (hasGeminiKey) {
                    keyInput.placeholder = "•••••••••••••••• (API Key đang hoạt động)";
                    keyStatus.className = "api-key-status success";
                    keyStatus.innerHTML = "<i class='fa fa-check-circle'></i> API Key hiện tại đang hoạt động tốt!";
                } else {
                    keyInput.placeholder = "Dán Gemini API Key vào đây...";
                    keyStatus.className = "api-key-status";
                    keyStatus.innerHTML = "Chưa cấu hình API Key. Chatbot đang chạy ở chế độ giả lập offline.";
                }
            }

            // Init placeholder on load
            updateKeyInputPlaceholder();

            if (openSettingsBtn) {
                openSettingsBtn.addEventListener("click", () => {
                    settingsPanel.classList.toggle("d-none");
                    if (!settingsPanel.classList.contains("d-none")) {
                        keyInput.focus();
                    }
                });
            }

            if (closeSettingsBtn) {
                closeSettingsBtn.addEventListener("click", () => {
                    settingsPanel.classList.add("d-none");
                });
            }

            if (saveKeyBtn) {
                saveKeyBtn.addEventListener("click", () => {
                    const apiKey = keyInput.value.trim();
                    
                    saveKeyBtn.disabled = true;
                    saveKeyBtn.innerHTML = "<i class='fa fa-spinner fa-spin'></i> Đang lưu...";
                    keyStatus.innerHTML = "";

                    fetch("/api/v1/ai-config", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                        },
                        body: JSON.stringify({ api_key: apiKey })
                    })
                    .then(res => res.json())
                    .then(data => {
                        saveKeyBtn.disabled = false;
                        saveKeyBtn.innerHTML = "Lưu cấu hình";

                        if (data.success) {
                            hasGeminiKey = apiKey !== "";
                            updateKeyInputPlaceholder();
                            keyStatus.className = "api-key-status success";
                            keyStatus.innerHTML = `<i class='fa fa-check-circle'></i> ${data.message}`;
                            keyInput.value = "";
                            
                            // Automatically close settings after 1.5s
                            setTimeout(() => {
                                settingsPanel.classList.add("d-none");
                            }, 1500);
                        } else {
                            keyStatus.className = "api-key-status error";
                            keyStatus.innerHTML = `<i class='fa fa-times-circle'></i> Lỗi: ${data.message}`;
                        }
                    })
                    .catch(err => {
                        saveKeyBtn.disabled = false;
                        saveKeyBtn.innerHTML = "Lưu cấu hình";
                        keyStatus.className = "api-key-status error";
                        keyStatus.innerHTML = "<i class='fa fa-times-circle'></i> Lỗi kết nối máy chủ!";
                    });
                });
            }

            let isTyping = false;

            // Toggle Panel
            chatBubble.addEventListener("click", () => {
                chatPanel.classList.toggle("d-none");
                const isOpen = !chatPanel.classList.contains("d-none");
                sessionStorage.setItem('tafood_chat_open', isOpen ? 'true' : 'false');
                // Scroll to bottom when opening
                if (isOpen) {
                    scrollToBottom();
                    chatInput.focus();
                }
            });

            closeChat.addEventListener("click", () => {
                chatPanel.classList.add("d-none");
                sessionStorage.setItem('tafood_chat_open', 'false');
            });

            // Handle Suggestion Chips click & mouse drag-to-scroll for PC
            const chipsContainer = document.querySelector('.ai-chat-suggestions');
            let isDown = false;
            let startX;
            let scrollLeft;
            let hasMoved = false;

            if (chipsContainer) {
                chipsContainer.addEventListener('mousedown', (e) => {
                    isDown = true;
                    hasMoved = false;
                    startX = e.pageX - chipsContainer.offsetLeft;
                    scrollLeft = chipsContainer.scrollLeft;
                    chipsContainer.style.cursor = 'grabbing';
                });

                chipsContainer.addEventListener('mouseleave', () => {
                    isDown = false;
                    chipsContainer.style.cursor = 'grab';
                });

                chipsContainer.addEventListener('mouseup', () => {
                    isDown = false;
                    chipsContainer.style.cursor = 'grab';
                });

                chipsContainer.addEventListener('mousemove', (e) => {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - chipsContainer.offsetLeft;
                    const walk = (x - startX) * 1.5; // Scroll multiplier
                    if (Math.abs(x - startX) > 5) {
                        hasMoved = true;
                    }
                    chipsContainer.scrollLeft = scrollLeft - walk;
                });

                // Default cursor style
                chipsContainer.style.cursor = 'grab';
            }

            suggestionChips.forEach(chip => {
                chip.addEventListener("click", (e) => {
                    if (hasMoved) {
                        e.preventDefault();
                        e.stopPropagation();
                        return;
                    }
                    const text = chip.getAttribute("data-text");
                    sendMessage(text);
                });
            });

            // Handle Form Submit
            chatForm.addEventListener("submit", (e) => {
                e.preventDefault();
                const text = chatInput.value.trim();
                if (text) {
                    sendMessage(text);
                    chatInput.value = "";
                }
            });

            function scrollToBottom() {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            function saveChatToSession(sender, text, foods = []) {
                try {
                    let history = JSON.parse(sessionStorage.getItem('tafood_chat_history') || '[]');
                    history.push({ sender, text, foods });
                    sessionStorage.setItem('tafood_chat_history', JSON.stringify(history));
                } catch (e) {
                    console.error("Error saving chat history:", e);
                }
            }

            function appendUserMessage(text, saveToSession = true) {
                const msg = document.createElement("div");
                msg.className = "message user-message";
                msg.innerHTML = `
                    <div class="message-bubble">${escapeHtml(text)}</div>
                `;
                messagesContainer.appendChild(msg);
                scrollToBottom();

                if (saveToSession) {
                    saveChatToSession('user', text);
                }
            }

            function appendAiMessage(text, foods = [], saveToSession = true) {
                const msg = document.createElement("div");
                msg.className = "message ai-message";
                
                // Formatted text supporting simple markdowns (e.g. bold **text**)
                let formattedText = formatMarkdown(text);
                
                // Location Button Check & Replacement
                if (formattedText.includes("[LOCATION_CHOOSER]")) {
                    formattedText = formattedText.replace("[LOCATION_CHOOSER]", `
                        <div class="ai-loc-options">
                            <button id="aiShareLocationBtn" class="ai-share-loc-btn">
                                <i class="fa fa-map-marker-alt"></i> Tự động định vị (GPS) 📍
                            </button>
                            <button id="aiManualLocationBtn" class="ai-share-loc-btn" style="background: linear-gradient(135deg, #a7a7a7, #777777); color: #fff !important; box-shadow: none;">
                                <i class="fa fa-keyboard"></i> Nhập địa chỉ thủ công 🔍
                            </button>
                        </div>
                        <div id="aiManualAddressFormContainer" class="d-none">
                            <div class="manual-address-form">
                                <input type="text" id="manualAddressInput" placeholder="Nhập địa chỉ (ví dụ: Cầu Giấy, Hà Nội)..." class="manual-address-input">
                                <button id="submitManualAddressBtn" class="manual-address-submit-btn"><i class="fa fa-search"></i> Tìm</button>
                            </div>
                            <div id="manualAddressStatus" style="font-size: 11px; color: #ffbe33; margin-top: 5px; font-weight: 600; text-align: center;"></div>
                        </div>
                    `);
                }

                // Change Location Link Check & Replacement
                if (formattedText.includes("[CHANGE_LOCATION_LINK]")) {
                    formattedText = formattedText.replace("[CHANGE_LOCATION_LINK]", `
                        <button class="ai-change-loc-link-btn" style="background: none; border: none; color: #ffbe33; text-decoration: underline; font-size: 11px; cursor: pointer; padding: 0; font-weight: 600; margin-top: 8px; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fa fa-map-marker-alt"></i> Thay đổi / Chọn vị trí khác 📍
                        </button>
                    `);
                }

                // Order Track Button Check & Replacement
                if (formattedText.includes("[TRACK_ORDER_")) {
                    formattedText = formattedText.replace(/\[TRACK_ORDER_(\d+)\]/g, `
                        <button class="ai-track-order-btn" data-id="$1" style="background: #ffbe33; color: #222831; border: none; border-radius: 6px; padding: 2px 8px; font-size: 10px; font-weight: 700; cursor: pointer; margin-left: 6px; display: inline-flex; align-items: center; gap: 3px; transition: all 0.2s ease;">
                            <i class="fa fa-search"></i> Tra cứu 🔍
                        </button>
                    `);
                }

                // View Order Detail Button Check & Replacement
                if (formattedText.includes("[VIEW_ORDER_DETAIL_")) {
                    formattedText = formattedText.replace(/\[VIEW_ORDER_DETAIL_(\d+)\]/g, `
                        <button class="ai-view-order-detail-btn" data-id="$1" style="background: #28a745; color: #fff; border: none; border-radius: 6px; padding: 2.5px 8px; font-size: 10px; font-weight: 700; cursor: pointer; margin-left: 6px; display: inline-flex; align-items: center; gap: 3px; transition: all 0.2s ease;">
                            <i class="fa fa-eye"></i> Vào xem ➡️
                        </button>
                    `);
                }

                // Check Other Orders Link Check & Replacement
                if (formattedText.includes("[CHECK_OTHER_ORDERS_LINK]")) {
                    formattedText = formattedText.replace("[CHECK_OTHER_ORDERS_LINK]", `
                        <button class="ai-check-other-orders-btn" style="background: none; border: none; color: #ffbe33; text-decoration: underline; font-size: 11px; cursor: pointer; padding: 0; font-weight: 600; margin-top: 8px; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fa fa-list"></i> Xem các đơn hàng khác của bạn 📋
                        </button>
                    `);
                }

                // Go To Profile Button Check & Replacement
                if (formattedText.includes("[GO_TO_PROFILE]")) {
                    formattedText = formattedText.replace("[GO_TO_PROFILE]", `
                        <a href="/profile" class="ai-go-profile-btn" style="background: linear-gradient(135deg, #ffbe33, #ff9800); color: #1a1a2e; border: none; border-radius: 8px; padding: 5px 12px; font-size: 11px; font-weight: 700; cursor: pointer; margin-top: 10px; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; box-shadow: 0 2px 8px rgba(255,190,51,0.35); transition: all 0.2s ease;">
                            <i class="fa fa-user-circle"></i> Vào trang tài khoản 👤
                        </a>
                    `);
                }

                // Restaurant Menu Popup Button Check & Replacement
                if (formattedText.includes("[OPEN_RESTAURANT_MENU_")) {
                    formattedText = formattedText.replace(/\[OPEN_RESTAURANT_MENU_(\d+)\]/g, (match, resId) => {
                        return `
                            <button class="ai-open-res-menu-btn" onclick="window.showRestaurantMenuModal(${resId})" style="background: linear-gradient(135deg, #ffbe33, #ff9800); color: #1a1a2e; border: none; border-radius: 8px; padding: 6px 12px; font-size: 11px; font-weight: 700; cursor: pointer; margin-top: 10px; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 2px 8px rgba(255,190,51,0.35); transition: all 0.2s ease; outline: none; border: none;">
                                <i class="fa fa-list-alt"></i> Xem thực đơn đầy đủ 📋
                            </button>
                        `;
                    });
                }
                
                
                let foodHtml = '';
                if (foods && foods.length > 0) {
                    foodHtml += '<div class="ai-foods-container">';
                    foods.forEach(f => {
                        foodHtml += `
                            <div class="ai-food-card" onclick="if(!event.target.closest('.ai-food-add-btn') && !event.target.closest('.ai-food-res')) window.location.href='/food/${f.id}'">
                                <img src="${f.image}" alt="${f.name}" class="ai-food-img" onerror="this.src='/images/no-image.png'">
                                <div class="ai-food-info">
                                    <div class="ai-food-name" title="${f.name}">${f.name}</div>
                                    <div class="ai-food-res" title="${f.restaurant_name}" onclick="event.stopPropagation(); if(typeof ${f.restaurant_id} !== 'undefined' && ${f.restaurant_id}) window.showRestaurantMenuModal(${f.restaurant_id})" style="cursor: pointer;"><i class="fa fa-store"></i> ${f.restaurant_name}</div>
                                    <div class="ai-food-meta">
                                        <div class="ai-food-price">${f.price_formatted}</div>
                                        <button class="ai-food-add-btn" 
                                                data-id="${f.id}" 
                                                data-name="${escapeHtml(f.name)}" 
                                                data-price="${f.price}" 
                                                data-image="${f.image}">
                                            <i class="fa fa-cart-plus"></i> Mua ngay
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    foodHtml += '</div>';
                }

                msg.innerHTML = `
                    <div class="message-bubble">
                        <div>${formattedText}</div>
                        ${foodHtml}
                    </div>
                `;
                messagesContainer.appendChild(msg);

                // Geolocation & Manual Address Button Event Binders
                const shareLocBtn = msg.querySelector('#aiShareLocationBtn');
                const manualLocBtn = msg.querySelector('#aiManualLocationBtn');
                const manualForm = msg.querySelector('#aiManualAddressFormContainer');
                const submitManualBtn = msg.querySelector('#submitManualAddressBtn');
                const manualInput = msg.querySelector('#manualAddressInput');
                const manualStatus = msg.querySelector('#manualAddressStatus');

                if (shareLocBtn) {
                    shareLocBtn.addEventListener('click', () => {
                        shareLocBtn.disabled = true;
                        shareLocBtn.innerHTML = "<i class='fa fa-spinner fa-spin'></i> Đang quét định vị...";

                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(
                                (position) => {
                                    const lat = position.coords.latitude;
                                    const lng = position.coords.longitude;

                                    localStorage.setItem("ta_food_delivery_lat", lat);
                                    localStorage.setItem("ta_food_delivery_lng", lng);
                                    localStorage.setItem("ta_food_gps_authorized", "true");

                                    shareLocBtn.style.background = "linear-gradient(135deg, #28a745, #218838)";
                                    shareLocBtn.style.color = "#fff";
                                    shareLocBtn.style.boxShadow = "none";
                                    shareLocBtn.innerHTML = "<i class='fa fa-check-circle'></i> Định vị thành công!";

                                    // Nominatim reverse geocoding
                                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
                                        .then(res => res.json())
                                        .then(data => {
                                            if (data && data.display_name) {
                                                localStorage.setItem("ta_food_delivery_address", data.display_name);
                                            }
                                        })
                                        .catch(err => console.error(err))
                                        .finally(() => {
                                            setTimeout(() => {
                                                sendMessage("Tìm các quán ăn ngon gần tôi");
                                            }, 1000);
                                        });
                                },
                                (error) => {
                                    console.error("GPS error:", error);
                                    shareLocBtn.disabled = false;
                                    shareLocBtn.style.background = "#dc3545";
                                    shareLocBtn.style.color = "#fff";
                                    shareLocBtn.innerHTML = "<i class='fa fa-exclamation-circle'></i> Lấy vị trí thất bại. Hãy thử lại!";
                                }
                            );
                        } else {
                            shareLocBtn.innerHTML = "<i class='fa fa-exclamation-circle'></i> Trình duyệt không hỗ trợ GPS!";
                        }
                    });
                }

                if (manualLocBtn && manualForm) {
                    manualLocBtn.addEventListener('click', () => {
                        manualForm.classList.toggle('d-none');
                        if (!manualForm.classList.contains('d-none') && manualInput) {
                            manualInput.focus();
                        }
                    });
                }

                if (submitManualBtn && manualInput && manualStatus) {
                    submitManualBtn.addEventListener('click', () => {
                        const addressVal = manualInput.value.trim();
                        if (!addressVal) {
                            manualStatus.style.color = "#ef4444";
                            manualStatus.innerHTML = "<i class='fa fa-times-circle'></i> Vui lòng nhập địa chỉ!";
                            return;
                        }

                        submitManualBtn.disabled = true;
                        submitManualBtn.innerHTML = "<i class='fa fa-spinner fa-spin'></i>";
                        manualStatus.style.color = "#ffbe33";
                        manualStatus.innerHTML = "Đang tìm kiếm tọa độ địa lý...";

                        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(addressVal)}&limit=1`)
                            .then(res => res.json())
                            .then(data => {
                                submitManualBtn.disabled = false;
                                submitManualBtn.innerHTML = "Tìm";

                                if (data && data.length > 0) {
                                    const place = data[0];
                                    const lat = place.lat;
                                    const lng = place.lon;

                                    localStorage.setItem("ta_food_delivery_lat", lat);
                                    localStorage.setItem("ta_food_delivery_lng", lng);
                                    localStorage.setItem("ta_food_delivery_address", place.display_name);
                                    localStorage.setItem("ta_food_gps_authorized", "true");

                                    manualStatus.style.color = "#22c55e";
                                    manualStatus.innerHTML = "<i class='fa fa-check-circle'></i> Tìm thấy địa chỉ! Đang quét quán ăn ngon...";

                                    // Clear input
                                    manualInput.value = "";

                                    setTimeout(() => {
                                        sendMessage("Tìm các quán ăn ngon gần tôi");
                                    }, 1200);
                                } else {
                                    manualStatus.style.color = "#ef4444";
                                    manualStatus.innerHTML = "<i class='fa fa-times-circle'></i> Không tìm thấy địa chỉ này. Hãy thử lại chi tiết hơn!";
                                }
                            })
                            .catch(err => {
                                console.error("Geocoding error:", err);
                                submitManualBtn.disabled = false;
                                submitManualBtn.innerHTML = "Tìm";
                                manualStatus.style.color = "#ef4444";
                                manualStatus.innerHTML = "<i class='fa fa-times-circle'></i> Lỗi kết nối mạng khi tìm địa chỉ!";
                            });
                    });

                    // Add enter key trigger for input
                    manualInput.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            submitManualBtn.click();
                        }
                    });
                }

                // Add to Cart Button Event Binder for inside the Chatbox
                msg.querySelectorAll('.ai-food-add-btn').forEach(btn => {
                    btn.addEventListener('click', async (e) => {
                        e.stopPropagation();
                        e.preventDefault();
                        
                        const foodId = Number(btn.getAttribute('data-id'));
                        const foodImage = btn.getAttribute('data-image');
                        
                        // Check if the standard active cart api function is available on index/menu pages
                        if (typeof api === 'function' && typeof renderCart === 'function') {
                            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang thêm...';
                            btn.disabled = true;
                            
                            try {
                                // Trigger fly-to-cart animation if available
                                if (typeof flyToCart === 'function') {
                                    flyToCart(btn, foodImage);
                                }
                                
                                const data = await api('/cart', {
                                    method: 'POST',
                                    body: { MaMonAn: foodId, SoLuong: 1 }
                                });
                                renderCart(data);
                                
                                // Show corner toast notification if available
                                if (typeof showAlert === 'function') {
                                    showAlert('success', `Đã thêm <strong>${btn.getAttribute('data-name')}</strong> vào giỏ hàng!`);
                                }
                                
                                btn.innerHTML = '<i class="fa fa-check"></i> Đã thêm';
                                setTimeout(() => {
                                    btn.innerHTML = '<i class="fa fa-cart-plus"></i> Mua ngay';
                                    btn.disabled = false;
                                }, 1500);
                            } catch (err) {
                                console.error("Error adding to cart:", err);
                                if (typeof showAlert === 'function') {
                                    const serverMsg = (err && err.message) ? String(err.message) : '';
                                    if (/ngừng\s*bán/i.test(serverMsg) || err?.status === 409 || err?.status === 422) {
                                        showAlert('danger', 'Món này hiện đã ngừng bán hoặc không khả dụng.');
                                    } else {
                                        showAlert('danger', 'Thêm vào giỏ thất bại. Lỗi: ' + (serverMsg || 'Xem Console để biết chi tiết.'));
                                    }
                                }
                                btn.innerHTML = '<i class="fa fa-cart-plus"></i> Mua ngay';
                                btn.disabled = false;
                            }
                        } else {
                            // Fallback to traditional post redirect if on a sub-page without AJAX cart scripts
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '/cart';
                            form.style.display = 'none';
                            
                            const csrfInput = document.createElement('input');
                            csrfInput.name = '_token';
                            csrfInput.value = '{{ csrf_token() }}';
                            
                            const idInput = document.createElement('input');
                            idInput.name = 'MaMonAn';
                            idInput.value = foodId;
                            
                            const qtyInput = document.createElement('input');
                            qtyInput.name = 'SoLuong';
                            qtyInput.value = 1;
                            
                            form.appendChild(csrfInput);
                            form.appendChild(idInput);
                            form.appendChild(qtyInput);
                            
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });

                // Change Location Link Event Binder
                msg.querySelectorAll('.ai-change-loc-link-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        appendAiMessage("Dạ, để mình tìm kiếm chính xác các nhà hàng, quán ăn ngon nhất gần bạn, bạn hãy chọn cách xác định vị trí của mình bên dưới nhé! 👇\n\n[LOCATION_CHOOSER]");
                    });
                });

                // Order Track Button Event Binder
                msg.querySelectorAll('.ai-track-order-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        const orderId = btn.getAttribute('data-id');
                        sendMessage(`Kiểm tra đơn hàng #${orderId}`);
                    });
                });

                // View Order Detail Button Event Binder
                msg.querySelectorAll('.ai-view-order-detail-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        const orderId = btn.getAttribute('data-id');
                        window.location.href = `/orders/${orderId}`;
                    });
                });

                // Check Other Orders Event Binder
                msg.querySelectorAll('.ai-check-other-orders-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        sendMessage(`Kiểm tra đơn hàng`);
                    });
                });

                scrollToBottom();

                if (saveToSession) {
                    saveChatToSession('ai', text, foods);
                }
            }

            function showTypingIndicator() {
                if (isTyping) return;
                isTyping = true;
                const typing = document.createElement("div");
                typing.className = "message ai-message";
                typing.id = "aiTypingIndicator";
                typing.innerHTML = `
                    <div class="message-bubble">
                        <div class="typing-indicator">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                `;
                messagesContainer.appendChild(typing);
                scrollToBottom();
            }

            function removeTypingIndicator() {
                if (!isTyping) return;
                isTyping = false;
                const typing = document.getElementById("aiTypingIndicator");
                if (typing) {
                    typing.remove();
                }
            }

            async function sendMessage(text) {
                if (isTyping) return;
                
                appendUserMessage(text);
                showTypingIndicator();

                try {
                    const history = JSON.parse(sessionStorage.getItem('tafood_chat_history') || '[]');
                    const response = await fetch('/api/v1/ai-chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ 
                            message: text,
                            lat: localStorage.getItem("ta_food_delivery_lat"),
                            lng: localStorage.getItem("ta_food_delivery_lng"),
                            address: localStorage.getItem("ta_food_delivery_address"),
                            history: history.slice(-5)
                        })
                    });

                    const res = await response.json();
                    removeTypingIndicator();

                    if (res.success && res.data) {
                        appendAiMessage(res.data.message, res.data.recommended_foods);
                    } else {
                        appendAiMessage("Úi! Có lỗi nhỏ xảy ra khi kết nối trợ lý AI, bạn hỏi lại giúp mình nhé! 🥺");
                    }
                } catch (error) {
                    removeTypingIndicator();
                    console.error("AI chat error:", error);
                    appendAiMessage("Có lỗi đường truyền, vui lòng kiểm tra kết nối mạng của bạn nhé! 🌐");
                }
            }

            function escapeHtml(text) {
                return text
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            function formatMarkdown(text) {
                // Remove (ID: X) or ID: X annotations to clean up the UI
                let formatted = text.replace(/\s*\(?ID:\s*\d+\)?/gi, '');
                // Support bold markdown **text**
                formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                // Support newlines
                formatted = formatted.replace(/\n/g, '<br>');
                return formatted;
            }

            function loadChatHistory() {
                try {
                    const history = JSON.parse(sessionStorage.getItem('tafood_chat_history') || '[]');
                    if (history.length > 0) {
                        messagesContainer.innerHTML = '';
                        history.forEach(item => {
                            if (item.sender === 'user') {
                                appendUserMessage(item.text, false);
                            } else {
                                appendAiMessage(item.text, item.foods, false);
                            }
                        });
                    }
                    
                    const isChatOpen = sessionStorage.getItem('tafood_chat_open') === 'true';
                    if (isChatOpen) {
                        chatPanel.classList.remove("d-none");
                        scrollToBottom();
                    }
                } catch (e) {
                    console.error("Error loading chat history:", e);
                }
            }

            // Restore chat panel & history on page load
            loadChatHistory();
        });
    </script>
    @if(session('google_login_success'))
        <script>
            localStorage.setItem("user", JSON.stringify({!! json_encode(session('google_login_success')) !!}));
        </script>
    @endif
    @include('client.partials._cart-scripts')
</body>

</html>
