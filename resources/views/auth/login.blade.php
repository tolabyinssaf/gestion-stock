<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Connexion | Gestion du Stock</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap">
<style>
:root{
    --navy:#081e6b;
    --blue:#0478ef;
    --muted:#687795;
    --orange:#fe7a04
}
*{
    box-sizing:border-box
}
body{
    margin:0;
    font-family:Montserrat,Arial,sans-serif;
    background:#f7f8fa
}
button,input{
    font:inherit
}
.login-page{
    position:relative;
    min-height:100vh;
    min-height:100dvh;
    display:flex;
    align-items:center;
    padding:32px 0;
    isolation:isolate
}
.login-visual{
    position:absolute;
    inset:0 0 0 auto;
    width:57.2%;
    border-radius:32px 0 0 32px;
    background:#0058bc url("{{ asset('images/back.png') }}") center/cover no-repeat;
    overflow:hidden;
    z-index:-1
}
.visual-content{
    position:absolute;
    top:30%;
    left:12%;
    right:7%;
    color:white
}
.visual-heading{
    display:flex;
    align-items:center;
    gap:10px
}
.visual-heading svg{
    width:clamp(38px,3.5vw,58px);
    flex-shrink:0
}
.visual-heading h2{
    margin:0;
    font-size:clamp(21px,1.8vw,32px);
    line-height:1.2
}
.visual-content p{
    font-size:clamp(12px,1.05vw,16px);
    line-height:1.6;
    max-width:390px;
    margin:8px 0
}
.orange-line{
    width:55px;
    height:4px;
    background:var(--orange);
    border-radius:4px
}
.login-container{
    width:min(40vw,480px);
    margin-left:calc(47.5% - min(40vw,480px));
    display:flex;
    justify-content:flex-start;
    z-index:1
}
.login-card{
    width:100%;
    max-width:480px;
    background:white;
    border-radius:20px;
    padding:clamp(28px,2vw,34px);
    box-shadow:0 15px 48px #0003
}
.login-logo{
    text-align:center;
    margin-bottom:22px
}
.login-logo img{
    width:110px;
    height:auto
}
.login-header{
    margin-bottom:24px
}
.login-header h1{
    margin:0 0 8px;
    color:var(--navy);
    font-size:26px;
    letter-spacing:-.6px
}
.login-header p{
    margin:0;
    color:var(--muted);
    font-size:12px;
    line-height:1.6
}
.login-form{
    display:grid;
    gap:14px
}
.input-group{
    position:relative
}
.input-group input{
    width:100%;
    height:44px;
    padding:0 44px;
    border:1px solid #d5dff1;
    background:#f3f5fa;
    border-radius:8px;
    color:var(--navy);
    font-size:13px;
    outline:none
}
.input-group input:focus{
    border-color:var(--blue);
    box-shadow:0 0 0 3px #0478ef15
}
.input-icon,.password-toggle svg{
    width:18px;
    height:18px
}
.input-icon{
    position:absolute;
    left:14px;
    top:50%;
    transform:translateY(-50%);
    color:var(--muted);
    pointer-events:none
}
.password-toggle{
    position:absolute;
    right:8px;
    top:50%;
    transform:translateY(-50%);
    border:0;
    background:none;
    color:var(--muted);
    cursor:pointer;
    padding:8px;
    display:grid;
    place-items:center
}
.login-options{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    flex-wrap:wrap;
    margin:2px 0 8px;
    font-size:12px
}
.remember-option{
    display:flex;
    align-items:center;
    gap:8px;
    color:var(--muted);
    cursor:pointer
}
.switch{
    position:relative;
    width:34px;
    height:19px;
    flex-shrink:0
}
.switch input{
    position:absolute;
    inset:0;
    opacity:0;
    margin:0;
    cursor:pointer
}
.switch-slider{
    position:absolute;
    inset:0;
    background:#e5eaf2;
    border-radius:20px;
    pointer-events:none
}
.switch-slider:before{
    content:'';
    position:absolute;
    left:2px;
    top:2px;
    width:15px;
    height:15px;
    background:white;
    border-radius:50%;
    transition:transform .2s
}
.switch input:checked+.switch-slider{
    background:var(--blue)
}
.switch input:checked+.switch-slider:before{
    transform:translateX(15px)
}
.switch input:focus-visible+.switch-slider{
    outline:2px solid var(--blue);
    outline-offset:3px
}
.forgot-password{
    color:var(--blue);
    text-decoration:none
}
.forgot-password:hover{
    text-decoration:underline
}
.login-button{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    width:100%;
    min-height:44px;
    border:0;
    border-radius:8px;
    background:#083184;
    color:white;
    font-weight:700;
    font-size:14px;
    cursor:pointer
}
.login-button:hover{
    background:#014392
}
.login-button svg{
    width:18px;
    height:18px
}
.error{
    color:#b42318;
    font-size:12px;
    margin:0
}
.status{
    color:#087443;
    font-size:12px;
    margin:0
}
button:focus-visible,a:focus-visible{
    outline:2px solid var(--blue);
    outline-offset:3px
}
@media(max-width:1024px){
    .login-container{
        width:44%;
        margin-left:3.5%
    }
    .login-card{
        padding:26px
    }
    .visual-content{
        left:14%
    }
    .login-options{
        font-size:10px
    }
}
@media(max-width:767px){
    .login-page{
        justify-content:center;
        padding:24px
    }
    .login-visual{
        width:100%;
        border-radius:0
    }
    .login-visual:after{
        content:'';
        position:absolute;
        inset:0;
        background:#031f5655
    }
    .visual-content{
        display:none
    }
    .login-container{
        width:100%;
        margin:0;
        justify-content:center
    }
    .login-card{
        max-width:380px;
        padding:28px 24px;
        border-radius:16px
    }
    .input-group input{
        font-size:16px
    }
    .login-options{
        font-size:11px
    }
}
@media(max-width:360px){
    .login-page{
        padding:14px
    }
    .login-card{
        padding:22px 18px
    }
}
@media(prefers-reduced-motion:reduce){
    *{
        transition:none!important
    }
}
</style>
</head>
<body>
<main class="login-page">
<section class="login-visual" aria-label="Gestion du Stock"><div class="visual-content"><div class="visual-heading"><svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true"><path d="M32 3 45 11 32 19 19 11Z M19 11V27L32 35 45 27V11 M32 19V35 M19 27 32 35 19 43 6 35Z M6 35V51L19 59 32 51V35 M19 43V59 M45 27 58 35 45 43 32 35 M32 35V51L45 59 58 51V35 M45 43V59"/></svg><h2>Gestion du Stock</h2></div><p>Suivi des entrées et sorties, gestion des stocks et inventaires.</p><div class="orange-line"></div></div></section>
<section class="login-container"><div class="login-card">
<div class="login-logo"><img src="{{ asset('images/logo.png') }}" alt="Lassureur du Var"></div>
<header class="login-header"><h1>Bon retour !</h1><p>Connectez-vous à votre espace de gestion du stock.</p></header>
<form class="login-form" action="{{ route('login') }}" method="POST">
@csrf
@if(session('status'))<p class="status" role="status">{{ session('status') }}</p>@endif
<div class="input-group"><svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="7" r="3.5"/><path d="M5 21v-3a7 7 0 0 1 14 0v3"/></svg><input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Adresse e-mail" aria-label="Adresse e-mail" autocomplete="username" required></div>
@error('email')<p class="error" role="alert">{{ $message }}</p>@enderror
<div class="input-group"><svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V6a4 4 0 0 1 8 0v4"/></svg><input type="password" name="password" id="password" placeholder="Mot de passe" aria-label="Mot de passe" autocomplete="current-password" required><button type="button" class="password-toggle" id="togglePassword" aria-label="Afficher le mot de passe" aria-pressed="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/><path id="eyeSlash" d="M3 3 21 21" style="display:none"/></svg></button></div>
@error('password')<p class="error" role="alert">{{ $message }}</p>@enderror
<div class="login-options"><label class="remember-option"><span class="switch"><input type="checkbox" name="remember" value="1" @checked(old('remember'))><span class="switch-slider"></span></span>Se souvenir de moi</label>@if(Route::has('password.request'))<a class="forgot-password" href="{{ route('password.request') }}">Mot de passe oublié ?</a>@endif</div>
<button type="submit" class="login-button"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M14 4h5v16h-5 M3 12h12 M10 7l5 5-5 5"/></svg>Se connecter</button>
</form></div></section></main>
<script>const password=document.getElementById('password');const toggle=document.getElementById('togglePassword');toggle.addEventListener('click',()=>{const show=password.type==='password';password.type=show?'text':'password';document.getElementById('eyeSlash').style.display=show?'block':'none';toggle.setAttribute('aria-pressed',String(show));toggle.setAttribute('aria-label',show?'Masquer le mot de passe':'Afficher le mot de passe');});</script>
</body></html>
