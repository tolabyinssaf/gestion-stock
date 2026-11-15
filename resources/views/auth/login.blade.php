<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion — Stock Informatique</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
        }

        /* ── Full-screen background ── */
        .login-page {
            position: relative;
            width: 100%;
            height: 100%;
            background: url('/images/login.png') no-repeat center center / cover;
            display: flex;
            align-items: center;
        }

        /* ── Layout wrapper ── */
        .login-layout {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: clamp(60px, 15vw, 240px);
        }

        /* ── Left text block ── */
        .left-text {
            position: absolute;
            bottom: 160px;
            left: 100px;
            text-align: left;
            z-index: 10;
        }

        .left-text h1 {
            color: #ffffff;
            font-size: 1.85rem;
            font-weight: 800;
            margin-bottom: 8px;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.35);
            text-align: left;
        }

        .left-text p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.85rem;
            font-weight: 400;
            line-height: 1.55;
            max-width: 280px;
            text-shadow: 0 1px 6px rgba(0, 0, 0, 0.3);
            text-align: left;
        }

        /* ── Right card ── */
        .login-card {
            width: 460px;
            background: rgba(255, 255, 255, 0.97);
            border-radius: 28px;
            padding: 48px 44px 40px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.12), 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        /* ── Logo centered ── */
        .logo-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
        }

        .logo-area img {
            display: block;
            height: 80px;
            width: auto;
            margin: 0 auto;
        }

        /* ── App subtitle line ── */
        .app-title-line {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        .app-title-line .box-icon {
            width: 36px;
            height: 36px;
            min-width: 36px;
            background: #EDF2FA;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .app-title-line span {
            font-size: 1.02rem;
            color: #1a2a4a;
            font-weight: 500;
            line-height: 1.3;
        }

        .app-title-line span b {
            font-weight: 700;
        }

        /* ── Session status ── */
        .session-alert {
            margin-bottom: 16px;
            padding: 10px 14px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 10px;
            font-size: 0.83rem;
            color: #065f46;
        }

        /* ── Form input groups ── */
        .field {
            margin-bottom: 16px;
        }

        .input-box {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1.5px solid #dde3ee;
            border-radius: 14px;
            height: 52px;
            padding: 0 16px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input-box:focus-within {
            border-color: #014392;
            box-shadow: 0 0 0 3px rgba(1, 67, 146, 0.08);
        }

        .input-box .field-icon {
            flex-shrink: 0;
            width: 22px;
            height: 22px;
            margin-right: 12px;
            color: #9ca3b4;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .input-box .field-icon svg {
            display: block;
            width: 20px;
            height: 20px;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .input-box input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.9rem;
            color: #1a2a4a;
            padding: 0;
            height: 100%;
        }

        .input-box input::placeholder {
            color: #9ca3b4;
            font-weight: 400;
        }

        .eye-btn {
            flex-shrink: 0;
            background: none;
            border: none;
            cursor: pointer;
            color: #7a8599;
            padding: 4px;
            margin-left: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s;
        }

        .eye-btn:hover {
            color: #1a2a4a;
        }

        .eye-btn svg {
            display: block;
            width: 20px;
            height: 20px;
        }

        /* ── Error text ── */
        .field-error {
            margin-top: 6px;
            font-size: 0.78rem;
            color: #dc2626;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* ── Submit button ── */
        .btn-submit {
            width: 100%;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 8px;
            background: #0a1e4a;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s, transform 0.15s;
        }

        .btn-submit:hover {
            background: #014392;
            box-shadow: 0 6px 18px rgba(1, 67, 146, 0.3);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-submit svg {
            display: block;
            width: 20px;
            height: 20px;
        }

        /* spinner */
        @keyframes spin { to { transform: rotate(360deg); } }
        .spin-anim { animation: spin 0.8s linear infinite; }

        /* ── Bottom row ── */
        .bottom-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
        }

        .chk-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .chk-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border: 2px solid #cbd5e1;
            border-radius: 4px;
            accent-color: #014392;
            cursor: pointer;
        }

        .chk-label span {
            font-size: 0.82rem;
            color: #64748b;
            user-select: none;
        }

        .forgot {
            font-size: 0.82rem;
            color: #014392;
            font-weight: 600;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .forgot:hover {
            color: #0161b9;
        }

        /* ── Responsive ── */
        @media (max-width: 960px) {
            .login-layout {
                justify-content: center;
                padding: 20px;
            }

            .left-text {
                display: none;
            }
            .login-card {
                width: 100%;
                max-width: 440px;
                padding: 36px 30px 32px;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 28px 22px 24px;
                border-radius: 20px;
            }

            .logo-area img {
                height: 60px;
            }
        }
    </style>
</head>
<body>

    <div class="login-page">

        <!-- LEFT: Text fixed bottom-left -->
        <div class="left-text">
            <h1>Gestion du Stock</h1>
            <p>Suivi des entrées et sorties, gestion des stocks et inventaires.</p>
        </div>

        <div class="login-layout">
            <!-- RIGHT: White card -->
            <div class="login-card" x-data="loginForm()">

                <!-- Logo VAR du Lassureur — centré -->
                <div class="logo-area">
                    <img src="/images/logo.png" alt="VAR du Lassureur" />
                </div>

                <!-- Subtitle -->
                <div class="app-title-line">
                    <div class="box-icon">
                        <!-- Lucide LogIn icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#014392" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" x2="3" y1="12" y2="12"/>
                        </svg>
                    </div>
                    <span>Connectez-vous à <b>votre espace</b></span>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="session-alert">{{ session('status') }}</div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" @submit="handleSubmit">
                    @csrf

                    <!-- Nom d'utilisateur -->
                    <div class="field">
                        <div class="input-box">
                            <div class="field-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Nom d'utilisateur"
                            />
                        </div>
                        @error('email')
                            <p class="field-error">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Mot de passe -->
                    <div class="field">
                        <div class="input-box">
                            <div class="field-icon">
                                <svg viewBox="0 0 24 24">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </div>
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Mot de passe"
                            />
                            <button type="button" @click="showPassword = !showPassword" class="eye-btn" tabindex="-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                                     x-show="!showPassword">
                                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                                     x-show="showPassword" style="display:none;">
                                    <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/>
                                    <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/>
                                    <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/>
                                    <path d="m2 2 20 20"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="field-error">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <button type="submit" :disabled="isLoading" class="btn-submit">
                        <template x-if="!isLoading">
                            <span style="display:inline-flex;align-items:center;gap:10px;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                    <polyline points="10 17 15 12 10 7"/>
                                    <line x1="15" x2="3" y1="12" y2="12"/>
                                </svg>
                                Se connecter
                            </span>
                        </template>
                        <template x-if="isLoading">
                            <span style="display:inline-flex;align-items:center;gap:10px;">
                                <svg class="spin-anim" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"/>
                                    <path fill="currentColor" opacity="0.75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                Connexion en cours...
                            </span>
                        </template>
                    </button>

                    <!-- Remember + Forgot -->
                    <div class="bottom-row">
                        <label class="chk-label">
                            <input id="remember_me" type="checkbox" name="remember" />
                            <span>Se souvenir de moi</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot">Mot de passe oublié ?</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    @vite(['resources/js/app.js'])
    <script>
        function loginForm() {
            return {
                showPassword: false,
                isLoading: false,
                handleSubmit() {
                    this.isLoading = true;
                }
            }
        }
    </script>
</body>
</html>
