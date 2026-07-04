<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to VehicleServe</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #0b0f19 0%, #111827 50%, #1f2937 100%);
            --primary-glow: rgba(59, 130, 246, 0.15);
            --admin-accent: #3b82f6;
            --admin-hover: #2563eb;
            --user-accent: #10b981;
            --user-hover: #059669;
            --workshop-accent: #f59e0b;
            --workshop-hover: #d97706;
        }

        body {
            background: linear-gradient(rgba(11, 15, 25, 0.75), rgba(17, 24, 39, 0.85)), url('{{ asset('images/background.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #f3f4f6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
            margin: 0;
            overflow-x: hidden;
        }

        /* Ambient background glow elements */
        .ambient-glow-1 {
            position: absolute;
            top: 20%;
            left: 15%;
            width: 300px;
            height: 300px;
            background: rgba(59, 130, 246, 0.08);
            border-radius: 50%;
            filter: blur(120px);
            z-index: 0;
            pointer-events: none;
        }

        .ambient-glow-2 {
            position: absolute;
            bottom: 20%;
            right: 15%;
            width: 350px;
            height: 350px;
            background: rgba(16, 185, 129, 0.06);
            border-radius: 50%;
            filter: blur(140px);
            z-index: 0;
            pointer-events: none;
        }

        .container {
            max-width: 1000px;
            z-index: 10;
        }

        .header-section {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .header-logo {
            font-size: 2.8rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            background: linear-gradient(to right, #3b82f6, #60a5fa, #10b981, #34d399);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header-logo i {
            background: linear-gradient(135deg, #3b82f6, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-subtitle {
            font-size: 1.15rem;
            color: #9ca3af;
            font-weight: 400;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .portal-card {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 3rem 2.25rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        }

        .portal-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(800px circle at var(--mouse-x, 0) var(--mouse-y, 0), rgba(255,255,255,0.06), transparent 40%);
            z-index: 1;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.5s;
        }

        .portal-card:hover::before {
            opacity: 1;
        }

        .portal-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.7);
        }

        .portal-card.admin-card:hover {
            border-color: rgba(59, 130, 246, 0.4);
            box-shadow: 0 20px 40px -15px rgba(59, 130, 246, 0.15), 0 0 0 1px rgba(59, 130, 246, 0.2);
        }

        .portal-card.workshop-card:hover {
            border-color: rgba(245, 158, 11, 0.4);
            box-shadow: 0 20px 40px -15px rgba(245, 158, 11, 0.15), 0 0 0 1px rgba(245, 158, 11, 0.2);
        }

        .portal-card.user-card:hover {
            border-color: rgba(16, 185, 129, 0.4);
            box-shadow: 0 20px 40px -15px rgba(16, 185, 129, 0.15), 0 0 0 1px rgba(16, 185, 129, 0.2);
        }

        .icon-wrapper {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 2rem;
            position: relative;
            z-index: 2;
        }

        .admin-card .icon-wrapper {
            background: rgba(59, 130, 246, 0.12);
            color: var(--admin-accent);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .workshop-card .icon-wrapper {
            background: rgba(245, 158, 11, 0.12);
            color: var(--workshop-accent);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .user-card .icon-wrapper {
            background: rgba(16, 185, 129, 0.12);
            color: var(--user-accent);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .portal-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
            color: #ffffff;
            z-index: 2;
        }

        .portal-desc {
            color: #9ca3af;
            font-size: 0.975rem;
            line-height: 1.6;
            margin-bottom: 2.5rem;
            z-index: 2;
        }

        .btn-portal {
            width: 100%;
            padding: 0.875rem 1.5rem;
            font-size: 1.05rem;
            font-weight: 600;
            border-radius: 14px;
            transition: all 0.2s ease;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            z-index: 2;
        }

        .btn-admin-primary {
            background: var(--admin-accent);
            color: white;
            border: none;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
        }

        .btn-admin-primary:hover {
            background: var(--admin-hover);
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }

        .btn-user-primary {
            background: var(--user-accent);
            color: white;
            border: none;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
        }

        .btn-user-primary:hover {
            background: var(--user-hover);
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-workshop-primary {
            background: var(--workshop-accent);
            color: white;
            border: none;
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.3);
        }

        .btn-workshop-primary:hover {
            background: var(--workshop-hover);
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }

        .btn-user-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #e5e7eb;
            border: 1px solid rgba(255, 255, 255, 0.15);
            margin-top: 0.75rem;
        }

        .btn-user-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .footer {
            margin-top: 5rem;
            font-size: 0.85rem;
            color: #4b5563;
            text-align: center;
            z-index: 10;
        }

        .footer a {
            color: #6b7280;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer a:hover {
            color: #9ca3af;
        }

        /* Auto repair animation centerpiece */
        .repair-animation-wrapper {
            position: relative;
            width: 100%;
            height: 180px;
            background: rgba(17, 24, 39, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 24px;
            margin: 0 auto 3.5rem auto;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                inset 0 0 25px rgba(0, 0, 0, 0.75),
                0 15px 35px -15px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .animation-road {
            position: absolute;
            bottom: 30px;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(255, 255, 255, 0.08) 15%, 
                rgba(255, 255, 255, 0.08) 85%, 
                transparent 100%
            );
            border-bottom: 2px dashed rgba(255, 255, 255, 0.04);
            z-index: 1;
        }

        /* Cars base styles */
        .car {
            position: absolute;
            bottom: 35px;
            width: 120px;
            height: 51px;
            z-index: 4;
            transform-origin: center bottom;
            pointer-events: none;
        }

        /* Wrecked Car Specific Styles */
        .wrecked-car {
            left: -150px;
            opacity: 0;
            animation: wreckedCarMove 60s cubic-bezier(0.25, 0.8, 0.25, 1) infinite;
        }

        /* Wobbly bounce for wrecked car to feel broken */
        .wrecked-car svg {
            animation: wreckedBounce 0.5s ease-in-out infinite;
        }

        /* Fixed Car Specific Styles */
        .fixed-car {
            left: calc(50% - 60px);
            opacity: 0;
            animation: fixedCarMove 60s cubic-bezier(0.25, 0.8, 0.25, 1) infinite;
        }

        /* Smooth floating motion for repaired car */
        .fixed-car svg {
            animation: smoothRide 1.2s ease-in-out infinite;
        }

        /* Garage/Service Portal Styles */
        .garage-portal {
            position: absolute;
            left: 50%;
            bottom: 25px;
            transform: translateX(-50%);
            width: 150px;
            height: 105px;
            background: rgba(17, 24, 39, 0.9);
            border: 2px solid rgba(255, 255, 255, 0.08);
            border-bottom: none;
            border-radius: 16px 16px 0 0;
            box-shadow: 
                0 10px 30px rgba(0, 0, 0, 0.8),
                0 0 20px rgba(59, 130, 246, 0.05),
                inset 0 0 15px rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            overflow: hidden;
            z-index: 5;
        }

        /* Glassmorphic overlay for the garage portal */
        .garage-portal::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.04) 0%, transparent 60%);
            pointer-events: none;
            z-index: 10;
        }

        /* Garage side columns/pillars */
        .garage-pillar-left, .garage-pillar-right {
            position: absolute;
            bottom: 0;
            width: 6px;
            height: 85px;
            background: linear-gradient(90deg, #374151, #1e293b);
            border: 1px solid rgba(255, 255, 255, 0.05);
            z-index: 9;
        }
        .garage-pillar-left { 
            left: 0; 
            border-radius: 0 3px 0 0; 
            border-left: none;
        }
        .garage-pillar-right { 
            right: 0; 
            border-radius: 3px 0 0 0; 
            border-right: none;
        }

        /* Garage roll-up door */
        .garage-door {
            position: absolute;
            left: 5px;
            right: 5px;
            bottom: 0;
            height: 100%;
            background: repeating-linear-gradient(
                0deg,
                rgba(30, 41, 59, 0.95),
                rgba(30, 41, 59, 0.95) 6px,
                rgba(15, 23, 42, 0.45) 6px,
                rgba(15, 23, 42, 0.45) 12px
            );
            border-radius: 8px 8px 0 0;
            border-top: 3px solid #475569;
            box-shadow: inset 0 2px 5px rgba(255,255,255,0.05);
            z-index: 7;
            animation: garageDoorShutter 60s ease-in-out infinite;
        }

        /* Status LED indicator */
        .portal-indicator {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #4b5563;
            z-index: 9;
            box-shadow: 0 0 0 rgba(0, 0, 0, 0);
            transition: all 0.3s ease;
            animation: portalLightColor 60s ease-in-out infinite;
        }

        /* Glass status window on the front wall */
        .garage-window {
            position: absolute;
            top: 32px;
            left: 50%;
            transform: translateX(-50%);
            width: 90px;
            height: 22px;
            background: rgba(15, 23, 42, 0.65);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-radius: 6px;
            z-index: 8;
            box-shadow: 
                inset 0 2px 5px rgba(0, 0, 0, 0.8),
                0 0 10px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }

        /* Robotic welding / interior glow effects */
        .garage-interior-glow {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            z-index: 2;
            animation: interiorGlowEffect 60s ease-in-out infinite;
        }

        /* Engine smoke particles for the wrecked car */
        .smoke-puff {
            fill: #9ca3af;
            opacity: 0;
            transform-origin: center;
        }

        .p1 { animation: puffSmoke 1.2s ease-out infinite; }
        .p2 { animation: puffSmoke 1.2s ease-out infinite 0.4s; }
        .p3 { animation: puffSmoke 1.2s ease-out infinite 0.8s; }

        /* Sparks container */
        .sparks-container {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 3;
            pointer-events: none;
        }

        .spark {
            position: absolute;
            width: 3px;
            height: 3px;
            background: #fbbf24;
            border-radius: 50%;
            opacity: 0;
            filter: drop-shadow(0 0 2px #f59e0b);
        }

        .spark-1 { animation: spark1Effect 60s linear infinite; }
        .spark-2 { animation: spark2Effect 60s linear infinite; }
        .spark-3 { animation: spark3Effect 60s linear infinite; }
        .spark-4 { animation: spark4Effect 60s linear infinite; }

        /* Sparkles for fixed car */
        .sparkle {
            transform-origin: center;
            opacity: 0;
        }
        .sp1 { animation: shineSparkle 1.2s ease-in-out infinite; }
        .sp2 { animation: shineSparkle 1.2s ease-in-out infinite 0.6s; }

        /* --- ANIMATION KEYFRAMES --- */

        /* 1. Wrecked Car Motion */
        @keyframes wreckedCarMove {
            0% { left: -140px; opacity: 0; }
            1% { opacity: 1; }
            8.33% { left: calc(50% - 60px); opacity: 1; } /* Enters and centers inside garage (5s) */
            10% { left: calc(50% - 60px); opacity: 0; }  /* Fades out as door closes (6s) */
            11.67% { left: calc(50% - 60px); opacity: 0; }
            100% { left: calc(50% - 60px); opacity: 0; }
        }

        /* Wrecked car shakiness */
        @keyframes wreckedBounce {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            25% { transform: translateY(-2px) rotate(-1.5deg); }
            50% { transform: translateY(1px) rotate(1deg); }
            75% { transform: translateY(-1px) rotate(-0.5deg); }
        }

        /* Wobbly wheels */
        .wobbly-wheel-1 {
            animation: wobbleWheel1 0.4s linear infinite;
            transform-origin: 36px 51px;
        }
        .wobbly-wheel-2 {
            animation: wobbleWheel2 0.4s linear infinite;
            transform-origin: 90px 51px;
        }

        @keyframes wobbleWheel1 {
            0% { transform: rotate(0deg) translate(0px, 0px); }
            50% { transform: rotate(180deg) translate(-1px, 1px); }
            100% { transform: rotate(360deg) translate(0px, 0px); }
        }
        @keyframes wobbleWheel2 {
            0% { transform: rotate(0deg) translate(0px, 0px); }
            50% { transform: rotate(180deg) translate(1px, -1px); }
            100% { transform: rotate(360deg) translate(0px, 0px); }
        }

        /* 2. Fixed Car Motion */
        @keyframes fixedCarMove {
            0% { left: calc(50% - 60px); opacity: 0; }
            23.33% { left: calc(50% - 60px); opacity: 0; } /* Under repair (14s) */
            25% { left: calc(50% - 60px); opacity: 1; }    /* Visible when door opens (15s) */
            26.67% { left: calc(50% - 60px); opacity: 1; }  /* Starts driving (16s) */
            36.67% { left: 105%; opacity: 1; }             /* Offscreen (22s) */
            37.5% { opacity: 0; }
            100% { left: 105%; opacity: 0; }
        }

        /* Smooth ride for repaired car */
        @keyframes smoothRide {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-1.5px); }
        }

        /* Spinning wheels */
        .spinning-wheel-1 {
            animation: spinWheel 0.25s linear infinite;
            transform-origin: 36px 51px;
        }
        .spinning-wheel-2 {
            animation: spinWheel 0.25s linear infinite;
            transform-origin: 90px 51px;
        }

        @keyframes spinWheel {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* 3. Garage Door Shutter */
        @keyframes garageDoorShutter {
            0% { height: 100%; } /* Closed */
            5% { height: 100%; } /* Closed */
            8.33% { height: 12%; } /* Fully Open (5s) */
            11.67% { height: 12%; } /* Open (7s) */
            15% { height: 100%; } /* Closed/Repairing (9s) */
            23.33% { height: 100%; } /* Repairing (14s) */
            26.67% { height: 12%; } /* Fully Open for Exit (16s) */
            36.67% { height: 12%; } /* Open (22s) */
            40% { height: 100%; } /* Closed (24s) */
            100% { height: 100%; } /* Remains Closed */
        }

        /* 4. Indicator Status Light */
        @keyframes portalLightColor {
            0% { background: #4b5563; box-shadow: none; } 
            5% { background: #10b981; box-shadow: 0 0 8px #10b981, 0 0 15px rgba(16,185,129,0.4); } /* Green (Open) */
            11.67% { background: #10b981; }
            13.33% { background: #ef4444; box-shadow: 0 0 8px #ef4444, 0 0 15px rgba(239,68,68,0.4); } /* Red (Repairing) */
            23.33% { background: #ef4444; }
            25% { background: #10b981; box-shadow: 0 0 8px #10b981, 0 0 15px rgba(16,185,129,0.4); } /* Green (Done) */
            36.67% { background: #10b981; }
            40% { background: #4b5563; box-shadow: none; } 
            100% { background: #4b5563; }
        }

        /* 5. Interior Glow effect during welding */
        @keyframes interiorGlowEffect {
            0% { opacity: 0; }
            13.33% { opacity: 0; }
            15% { opacity: 0.8; background: radial-gradient(circle, rgba(59, 130, 246, 0.75) 0%, transparent 70%); } /* Blue flash */
            17% { opacity: 0.3; }
            18.5% { opacity: 0.9; background: radial-gradient(circle, rgba(245, 158, 11, 0.8) 0%, transparent 70%); } /* Orange sparks glow */
            20% { opacity: 0.4; }
            21.5% { opacity: 0.95; background: radial-gradient(circle, rgba(16, 185, 129, 0.8) 0%, transparent 70%); } /* Green finish glow */
            23.33% { opacity: 0; }
            100% { opacity: 0; }
        }

        /* Smoke puff animation */
        @keyframes puffSmoke {
            0% { transform: translate(0, 0) scale(0.6); opacity: 0.7; }
            50% { transform: translate(-10px, -15px) scale(1.1); opacity: 0.4; }
            100% { transform: translate(-20px, -25px) scale(1.6); opacity: 0; }
        }

        /* Sparks flying animation */
        @keyframes spark1Effect {
            0%, 14.5% { opacity: 0; transform: translate(0, 0); }
            15% { opacity: 1; transform: translate(40px, 50px) scale(1); }
            16.5% { opacity: 0.8; transform: translate(20px, 35px) scale(1.4); }
            18% { opacity: 0; transform: translate(10px, 20px) scale(0.4); }
            19.5% { opacity: 1; transform: translate(55px, 55px) scale(1.1); }
            21% { opacity: 0.7; transform: translate(35px, 30px) scale(1.3); }
            22.5% { opacity: 0; transform: translate(15px, 15px) scale(0.3); }
            100% { opacity: 0; }
        }
        @keyframes spark2Effect {
            0%, 15.5% { opacity: 0; transform: translate(0, 0); }
            16% { opacity: 1; transform: translate(80px, 65px) scale(1); }
            17.5% { opacity: 0.7; transform: translate(100px, 50px) scale(1.3); }
            19% { opacity: 0; transform: translate(110px, 40px) scale(0.4); }
            20.5% { opacity: 1; transform: translate(65px, 60px) scale(1); }
            22% { opacity: 0.6; transform: translate(85px, 40px) scale(1.2); }
            23% { opacity: 0; transform: translate(100px, 25px) scale(0.3); }
            100% { opacity: 0; }
        }
        @keyframes spark3Effect {
            0%, 16.5% { opacity: 0; transform: translate(0, 0); }
            17% { opacity: 1; transform: translate(60px, 40px) scale(1.2); }
            18.5% { opacity: 0.9; transform: translate(75px, 25px) scale(1.5); }
            20% { opacity: 0; transform: translate(85px, 15px) scale(0.5); }
            21.5% { opacity: 1; transform: translate(50px, 45px) scale(1); }
            22.5% { opacity: 0.8; transform: translate(30px, 30px) scale(1.2); }
            23.33% { opacity: 0; transform: translate(10px, 20px) scale(0.3); }
            100% { opacity: 0; }
        }
        @keyframes spark4Effect {
            0%, 14.5% { opacity: 0; transform: translate(0, 0); }
            16% { opacity: 1; transform: translate(35px, 60px) scale(1); }
            17.5% { opacity: 0.8; transform: translate(55px, 40px) scale(1.4); }
            19% { opacity: 0; transform: translate(75px, 25px) scale(0.4); }
            20.5% { opacity: 1; transform: translate(70px, 50px) scale(1); }
            22% { opacity: 0.5; transform: translate(90px, 30px) scale(1.2); }
            23% { opacity: 0; transform: translate(100px, 20px) scale(0.2); }
            100% { opacity: 0; }
        }

        /* Repaired car sparkles */
        @keyframes shineSparkle {
            0%, 100% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); opacity: 1; }
        }

        /* Mobile scaling */
        @media (max-width: 576px) {
            .repair-animation-wrapper {
                transform: scale(0.85);
                margin-bottom: 2rem;
                height: 160px;
            }
        }
    </style>
</head>
<body>

    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="container">
        <!-- Header -->
        <div class="header-section">
            <div class="header-logo">
                <i class="bi bi-car-front-fill"></i>
                <span>VehicleServe</span>
            </div>
            <div class="header-subtitle">
                A Smart Vehicle Service & Maintenance History System. Please select your account type below to access the appropriate dashboard tools.
            </div>
        </div>

        <!-- Repair Animation Centerpiece -->
        <div class="repair-animation-wrapper">
            <!-- Road/track -->
            <div class="animation-road"></div>
            
            <!-- Wrecked Car (Moving left to center) -->
            <div class="car wrecked-car">
                <svg viewBox="0 0 140 60" width="100%" height="100%">
                    <!-- Smoke rising from engine (front is right) -->
                    <g class="engine-smoke">
                        <circle cx="102" cy="22" r="3.5" class="smoke-puff p1" />
                        <circle cx="106" cy="18" r="5" class="smoke-puff p2" />
                        <circle cx="98" cy="15" r="4" class="smoke-puff p3" />
                    </g>
                    <!-- Wrecked Car Body (dull gray-rust) -->
                    <path d="M12,42 L18,29 C20,26 28,24 38,24 L68,24 C75,24 82,24 88,31 L94,34 C101,35 108,37 112,41 L112,48 C112,50 110,51 108,51 L99,51 A12,12 0 0,0 81,51 L45,51 A12,12 0 0,0 27,51 L17,51 C14,51 12,50 12,48 Z" fill="#4b5563" stroke="#374151" stroke-width="2" />
                    <!-- Rust spots / Scratches -->
                    <path d="M22,33 Q27,30 32,34" stroke="#b45309" stroke-width="2" fill="none" stroke-linecap="round" />
                    <path d="M54,29 Q58,34 62,30" stroke="#b45309" stroke-width="1.5" fill="none" stroke-linecap="round" />
                    <path d="M96,39 L106,37" stroke="#1f2937" stroke-width="2" stroke-linecap="round" />
                    <path d="M98,42 L104,41" stroke="#b45309" stroke-width="1.5" stroke-linecap="round" />
                    <!-- Cracked Windshield -->
                    <path d="M84,26 L92,33 M88,28 L95,29 M89,32 L93,31" stroke="#d1d5db" stroke-width="1" stroke-linecap="round" />
                    <!-- Broken Headlight (front right) -->
                    <circle cx="110" cy="44" r="2.5" fill="#374151" stroke="#4b5563" />
                    <!-- Wobbly wheels -->
                    <g class="wobbly-wheel-1">
                        <circle cx="36" cy="51" r="9" fill="#1f2937" stroke="#4b5563" stroke-width="2" />
                        <circle cx="36" cy="51" r="3" fill="#6b7280" />
                        <line x1="36" y1="42" x2="36" y2="60" stroke="#374151" stroke-width="1.5" />
                        <line x1="27" y1="51" x2="45" y2="51" stroke="#374151" stroke-width="1.5" />
                    </g>
                    <g class="wobbly-wheel-2">
                        <circle cx="90" cy="51" r="9" fill="#1f2937" stroke="#4b5563" stroke-width="2" />
                        <circle cx="90" cy="51" r="3" fill="#6b7280" />
                        <line x1="90" y1="42" x2="90" y2="60" stroke="#374151" stroke-width="1.5" />
                        <line x1="81" y1="51" x2="99" y2="51" stroke="#374151" stroke-width="1.5" />
                    </g>
                </svg>
            </div>
            
            <!-- Repair Portal (Center) -->
            <div class="garage-portal">
                <!-- Status LED -->
                <div class="portal-indicator"></div>
                
                <!-- Front Windows showing laser glows inside -->
                <div class="garage-window"></div>
                
                <!-- Roll-up grate door -->
                <div class="garage-door"></div>
                
                <!-- Interior light show -->
                <div class="garage-interior-glow"></div>
                
                <!-- Sparks -->
                <div class="sparks-container">
                    <div class="spark spark-1"></div>
                    <div class="spark spark-2"></div>
                    <div class="spark spark-3"></div>
                    <div class="spark spark-4"></div>
                </div>

                <!-- Side pillars -->
                <div class="garage-pillar-left"></div>
                <div class="garage-pillar-right"></div>
            </div>
            
            <!-- Repaired Car (Emerges and moves right) -->
            <div class="car fixed-car">
                <svg viewBox="0 0 140 60" width="100%" height="100%">
                    <defs>
                        <linearGradient id="carGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#10b981" />
                            <stop offset="50%" stop-color="#059669" />
                            <stop offset="100%" stop-color="#34d399" />
                        </linearGradient>
                        <linearGradient id="glassGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#60a5fa" />
                            <stop offset="100%" stop-color="#1e3a8a" />
                        </linearGradient>
                        <linearGradient id="beamGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="rgba(253, 224, 71, 0.35)" />
                            <stop offset="100%" stop-color="rgba(253, 224, 71, 0)" />
                        </linearGradient>
                    </defs>
                    <!-- Headlight Beam -->
                    <path d="M112,43 L138,36 L138,50 Z" fill="url(#beamGrad)" />
                    <!-- Sparkles -->
                    <g class="car-sparkles">
                        <path d="M20,15 L22,18 L25,18 L22,19 L23,22 L20,20 L17,22 L18,19 L15,18 L18,18 Z" fill="#fbbf24" class="sparkle sp1" />
                        <path d="M55,10 L56,12 L58,12 L56,13 L57,15 L55,14 L53,15 L54,13 L52,12 L54,12 Z" fill="#ffffff" class="sparkle sp2" />
                    </g>
                    <!-- Repaired Car Body -->
                    <path d="M12,42 L18,29 C20,26 28,24 38,24 L68,24 C75,24 82,24 88,31 L94,34 C101,35 108,37 112,41 L112,48 C112,50 110,51 108,51 L99,51 A12,12 0 0,0 81,51 L45,51 A12,12 0 0,0 27,51 L17,51 C14,51 12,50 12,48 Z" fill="url(#carGrad)" stroke="#10b981" stroke-width="1" />
                    <!-- Body highlight line -->
                    <path d="M18,28 Q45,25 85,25" stroke="rgba(255, 255, 255, 0.3)" stroke-width="1.5" fill="none" />
                    <!-- Windows -->
                    <rect x="44" y="27" width="14" height="8" rx="2" fill="url(#glassGrad)" />
                    <path d="M62,27 L76,27 C78,27 82,29 83,31 L83,35 L62,35 Z" fill="url(#glassGrad)" />
                    <path d="M84,26 L91,33 L85,33 Z" fill="rgba(255,255,255,0.15)" />
                    <!-- Glowing Headlight -->
                    <circle cx="110" cy="44" r="2.5" fill="#fef08a" class="headlight-glow" />
                    <!-- Smooth spinning wheels -->
                    <g class="spinning-wheel-1">
                        <circle cx="36" cy="51" r="9" fill="#111827" stroke="#10b981" stroke-width="1.5" />
                        <circle cx="36" cy="51" r="3" fill="#e5e7eb" />
                        <line x1="36" y1="42" x2="36" y2="60" stroke="#4b5563" stroke-width="1" />
                        <line x1="27" y1="51" x2="45" y2="51" stroke="#4b5563" stroke-width="1" />
                    </g>
                    <g class="spinning-wheel-2">
                        <circle cx="90" cy="51" r="9" fill="#111827" stroke="#10b981" stroke-width="1.5" />
                        <circle cx="90" cy="51" r="3" fill="#e5e7eb" />
                        <line x1="90" y1="42" x2="90" y2="60" stroke="#4b5563" stroke-width="1" />
                        <line x1="81" y1="51" x2="99" y2="51" stroke="#4b5563" stroke-width="1" />
                    </g>
                </svg>
            </div>
        </div>

        <!-- Portals Grid -->
        <div class="row g-4 justify-content-center">
            <!-- Admin Portal -->
            <div class="col-lg-4 col-md-6">
                <div class="portal-card admin-card">
                    <div>
                        <div class="icon-wrapper">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <h3 class="portal-title">Administrator</h3>
                        <p class="portal-desc">
                            Access system administration configuration, verify workshop accounts, manage database entries, view system audit logs, and maintain user access controls.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('login', ['role' => 'admin']) }}" class="btn-portal btn-user-secondary">
                            Sign In with Password <i class="bi bi-box-arrow-in-right fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Workshop Portal -->
            <div class="col-lg-4 col-md-6">
                <div class="portal-card workshop-card">
                    <div>
                        <div class="icon-wrapper">
                            <i class="bi bi-shop"></i>
                        </div>
                        <h3 class="portal-title">Workshop Partner</h3>
                        <p class="portal-desc">
                            Register and manage your workshop branch, configure repair services, accept booking slots from users, update service bills, and receive payouts.
                        </p>
                    </div>
                    <div>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('login', ['role' => 'workshop']) }}" class="btn-portal btn-user-secondary mt-0 py-2.5 text-xs">
                                    Sign In <i class="bi bi-box-arrow-in-right ms-1"></i>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('register', ['role' => 'workshop']) }}" class="btn-portal btn-user-secondary mt-0 py-2.5 text-xs">
                                    Register <i class="bi bi-person-plus ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Portal -->
            <div class="col-lg-4 col-md-6">
                <div class="portal-card user-card">
                    <div>
                        <div class="icon-wrapper">
                            <i class="bi bi-car-front-fill"></i>
                        </div>
                        <h3 class="portal-title">Vehicle Owner</h3>
                        <p class="portal-desc">
                            Register vehicle profiles, search for nearby workshops using interactive maps, book repair slots, pay bills online, and submit complaints.
                        </p>
                    </div>
                    <div>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('login', ['role' => 'owner']) }}" class="btn-portal btn-user-secondary mt-0 py-2.5 text-xs">
                                    Sign In <i class="bi bi-box-arrow-in-right ms-1"></i>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('register', ['role' => 'owner']) }}" class="btn-portal btn-user-secondary mt-0 py-2.5 text-xs">
                                    Register <i class="bi bi-person-plus ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} VehicleServe. All rights reserved. <br>
            Designed with premium accessibility features.
        </div>
    </div>

    <!-- Mouse hover glow effect script -->
    <script>
        document.querySelectorAll('.portal-card').forEach(card => {
            card.addEventListener('mousemove', e => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                card.style.setProperty('--mouse-x', `${x}px`);
                card.style.setProperty('--mouse-y', `${y}px`);
            });
        });
    </script>
</body>
</html>
