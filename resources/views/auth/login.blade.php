
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Connexion | Gestion du Stock</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap">

    <style>
        :root {
            --navy: #08265e;
            --blue: #00a3ef;
            --muted: #52617b;
            --border: #dce4f0;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            width: 100%;
            min-height: 100%;
        }

        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background: #ffffff;
        }

        input,
        button {
            font: inherit;
        }

        /* BACKGROUND PRINCIPAL */

        .login-page {
            position: relative;
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;

            background-image: url("{{ asset('images/background.png') }}");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 100% 100%;

            display: flex;
            align-items: center;
        }

        /* POSITION DU FORMULAIRE */

        .login-container {
            width: 38%;
            min-height: 100vh;
            min-height: 100dvh;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 24px 0;
        }

        .login-card {
            width: 83%;
            max-width: 430px;

            background: transparent;
            border: none;
            border-radius: 0;
            box-shadow: none;

            padding: 0;
        }

        /* LOGO */

        .login-logo {
            display: flex;
            justify-content: center;
            align-items: center;

            margin-bottom: clamp(18px, 4vh, 38px);
        }

        .login-logo img {
            display: block;
            width: 52%;
            max-width: 220px;
            max-height: 18vh;
            height: auto;
            object-fit: contain;
        }

        /* TITRE */

        .login-header {
            margin-bottom: clamp(16px, 3vh, 28px);
        }

        .blue-line {
            width: 60px;
            height: 5px;
            background: var(--blue);
            border-radius: 10px;
            margin-bottom: 12px;
        }

        .login-header h1 {
            margin: 0 0 8px;
            font-size: clamp(26px, 3.4vw, 46px);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1px;
            color: var(--navy);
        }

        .login-header p {
            margin: 0;
            max-width: 340px;

            font-size: clamp(12px, 1.4vw, 17px);
            font-weight: 500;
            line-height: 1.5;
            color: var(--muted);
        }

        /* FORMULAIRE */

        .login-form {
            display: flex;
            flex-direction: column;
            gap: clamp(10px, 1.8vh, 16px);
        }

        .input-group {
            position: relative;
            width: 100%;
        }

        .input-group input {
            width: 100%;
            height: clamp(42px, 7vh, 58px);

            padding: 0 45px 0 54px;

            background: rgba(255, 255, 255, 0.7);
            border: 1.5px solid var(--border);
            border-radius: 16px;

            color: var(--navy);
            font-size: clamp(12px, 1.1vw, 15px);
            font-weight: 600;

            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .input-group input::placeholder {
            color: var(--muted);
            opacity: 1;
        }

        .input-group input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 4px rgba(0, 163, 239, 0.10);
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;

            width: 21px;
            height: 21px;

            transform: translateY(-50%);
            color: var(--muted);
            pointer-events: none;
        }

        /* AFFICHER LE MOT DE PASSE */

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 6px;
            background: transparent;
            border: none;

            color: var(--muted);
            cursor: pointer;
        }

        .password-toggle svg {
            width: 21px;
            height: 21px;
        }

        /* OPTIONS */

        .login-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;

            gap: 10px;
            margin: 2px 0 8px;

            font-size: clamp(10px, .95vw, 13px);
            font-weight: 600;
        }

        .remember-option {
            display: flex;
            align-items: center;
            gap: 8px;

            color: var(--muted);
            cursor: pointer;
        }

        .remember-option input {
            appearance: none;
            -webkit-appearance: none;

            display: grid;
            place-content: center;

            width: 19px;
            height: 19px;

            margin: 0;
            border: 1.5px solid #8491a5;
            border-radius: 4px;
            background: transparent;

            cursor: pointer;
        }

        .remember-option input:checked {
            background: var(--blue);
            border-color: var(--blue);
        }

        .remember-option input:checked::after {
            content: "";
            width: 5px;
            height: 9px;

            border: solid #ffffff;
            border-width: 0 2px 2px 0;

            transform: rotate(45deg);
        }

        .forgot-password {
            color: #0078ef;
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .forgot-password:hover {
            color: var(--navy);
        }

        /* BOUTON DE CONNEXION */

        .login-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;

            width: 100%;
            min-height: clamp(46px, 8vh, 64px);

            padding: 12px 20px;

            border: none;
            border-radius: 50px;

            background: linear-gradient(
                110deg,
                #00a9ee 0%,
                #0098f4 100%
            );

            color: #ffffff;
            font-size: clamp(14px, 1.35vw, 19px);
            font-weight: 700;

            cursor: pointer;
            transition: background .2s, transform .2s;
        }

        .login-button svg {
            width: 24px;
            height: 24px;
            flex-shrink: 0;
        }

        .login-button:hover {
            background: linear-gradient(
                110deg,
                #0093da,
                #007cdb
            );
            transform: translateY(-2px);
        }

        .login-button:active {
            transform: translateY(0);
        }

        /* MESSAGES LARAVEL */

        .error {
            margin: 0;
            color: #b42318;
            font-size: 12px;
            line-height: 1.5;
        }

        .status {
            margin: 0;
            color: #087443;
            font-size: 12px;
            line-height: 1.5;
        }

        button:focus-visible,
        a:focus-visible,
        input:focus-visible {
            outline: 2px solid var(--blue);
            outline-offset: 3px;
        }

        /* TABLETTE */

        @media (min-width: 769px) and (max-width: 1024px) {

            .login-container {
                width: 38%;
            }

            .login-card {
                width: 85%;
            }

            .login-logo img {
                width: 55%;
            }

            .login-header h1 {
                font-size: 30px;
            }

            .login-options {
                gap: 10px;
                font-size: 10px;
            }

            .input-group input {
                padding-left: 40px;
            }

            .input-icon {
                left: 12px;
                width: 18px;
                height: 18px;
            }
        }

        /* MOBILE */

        @media (max-width: 768px) {

            .login-page {
                background-image: none;
                background-color: #ffffff;

                justify-content: center;
                overflow: hidden;
            }

            .login-page::before {
                content: "";
                position: absolute;

                top: -160px;
                right: -160px;

                width: 290px;
                height: 290px;

                background: linear-gradient(
                    135deg,
                    #0478ef,
                    #00cef3
                );

                border-radius: 50%;
                pointer-events: none;
            }

            .login-page::after {
                content: "";
                position: absolute;

                bottom: -180px;
                left: -100px;

                width: 350px;
                height: 230px;

                background: linear-gradient(
                    135deg,
                    #0058bc,
                    #00cef3
                );

                border-radius: 50%;
                pointer-events: none;
            }

            .login-container {
                position: relative;
                z-index: 1;

                width: 100%;
                padding: 55px 26px;
            }

            .login-card {
                width: 100%;
                max-width: 400px;
            }

            .login-logo {
                margin-bottom: 36px;
            }

            .login-logo img {
                width: 160px;
                max-height: none;
            }

            .login-header {
                margin-bottom: 26px;
            }

            .login-header h1 {
                font-size: 36px;
            }

            .login-header p {
                font-size: 14px;
            }

            .blue-line {
                width: 50px;
                height: 4px;
            }

            .login-form {
                gap: 15px;
            }

            .input-group input {
                height: 54px;
                font-size: 16px;
                border-radius: 12px;
                padding-left: 48px;
            }

            .input-icon {
                left: 15px;
                width: 20px;
                height: 20px;
            }

            .login-options {
                font-size: 11px;
                gap: 12px;
            }

            .login-button {
                min-height: 56px;
                font-size: 16px;
            }
        }

        /* PETITS ÉCRANS */

        @media (max-width: 360px) {

            .login-container {
                padding: 40px 18px;
            }

            .login-header h1 {
                font-size: 30px;
            }

            .login-options {
                font-size: 10px;
            }
        }

        /* ACCESSIBILITÉ */

        @media (prefers-reduced-motion: reduce) {
            * {
                transition: none !important;
            }
        }
    </style>
</head>

<body>

<main class="login-page">

    <section class="login-container">

        <div class="login-card">

            <!-- LOGO -->

            <div class="login-logo">

                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="Lassureur du Var"
                >

            </div>

            <!-- TITRE -->

            <header class="login-header">

                <div class="blue-line"></div>

                <h1>Bienvenue</h1>

                <p>
                    Connectez-vous à votre espace de gestion du stock.
                </p>

            </header>

            <!-- FORMULAIRE LARAVEL -->

            <form
                class="login-form"
                action="{{ route('login') }}"
                method="POST"
            >

                @csrf

                @if(session('status'))

                    <p class="status" role="status">
                        {{ session('status') }}
                    </p>

                @endif

                <!-- ADRESSE EMAIL -->

                <div class="input-group">

                    <svg
                        class="input-icon"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path d="m3 7 9 6 9-6"/>
                    </svg>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        placeholder="Adresse e-mail"
                        aria-label="Adresse e-mail"
                        autocomplete="username"
                        required
                    >

                </div>

                @error('email')

                    <p class="error" role="alert">
                        {{ $message }}
                    </p>

                @enderror

                <!-- MOT DE PASSE -->

                <div class="input-group">

                    <svg
                        class="input-icon"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <rect x="5" y="10" width="14" height="11" rx="2"/>
                        <path d="M8 10V6a4 4 0 0 1 8 0v4"/>
                        <path d="M12 14v3"/>
                    </svg>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Mot de passe"
                        aria-label="Mot de passe"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        id="togglePassword"
                        aria-label="Afficher le mot de passe"
                        aria-pressed="false"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.6"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>

                            <path
                                id="eyeSlash"
                                d="M3 3 21 21"
                                style="display:none"
                            />
                        </svg>

                    </button>

                </div>

                @error('password')

                    <p class="error" role="alert">
                        {{ $message }}
                    </p>

                @enderror

                <!-- OPTIONS DE CONNEXION -->

                <div class="login-options">

                    <label class="remember-option">

                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            @checked(old('remember'))
                        >

                        <span>Se souvenir de moi</span>

                    </label>

                    @if(Route::has('password.request'))

                        <a
                            class="forgot-password"
                            href="{{ route('password.request') }}"
                        >
                            Mot de passe oublié ?
                        </a>

                    @endif

                </div>

                <!-- BOUTON -->

                <button type="submit" class="login-button">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M14 4h5a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-5"/>
                        <path d="M3 12h12"/>
                        <path d="m10 7 5 5-5 5"/>
                    </svg>

                    Se connecter

                </button>

            </form>

        </div>

    </section>

</main>

<script>
    const password = document.getElementById('password');
    const toggle = document.getElementById('togglePassword');
    const eyeSlash = document.getElementById('eyeSlash');

    toggle.addEventListener('click', function () {

        const show = password.type === 'password';

        password.type = show ? 'text' : 'password';

        eyeSlash.style.display = show ? 'block' : 'none';

        toggle.setAttribute(
            'aria-pressed',
            String(show)
        );

        toggle.setAttribute(
            'aria-label',
            show
                ? 'Masquer le mot de passe'
                : 'Afficher le mot de passe'
        );

    });
</script>

</body>
</html>
