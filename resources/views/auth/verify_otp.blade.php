<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verify OTP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {font-family: 'Inter', sans-serif; background: #1a1a2e; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; color:#fff;}
        .container {background:#16213e; padding:2rem; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.4); width:340px;}
        h2 {margin:0 0 1.5rem; font-weight:600; text-align:center;}
        input {width:100%; padding:0.75rem 1rem; border:none; border-radius:8px; margin-bottom:1rem; background:#0f0c29; color:#fff; font-size:1rem;}
        input::placeholder {color:#aaa;}
        button {width:100%; padding:0.75rem 1rem; background:#e94560; background:linear-gradient(45deg,#e94560,#0f0c29); border:none; border-radius:8px; color:#fff; font-size:1rem; font-weight:600; cursor:pointer; transition:transform 0.2s;}
        button:hover {transform:scale(1.02);}
        .error {color:#ff6b6b; margin-top:0.5rem; font-size:0.9rem;}
    </style>
</head>
<body>
<div class="container">
    <h2>Enter OTP</h2>
    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('auth.verify-otp.submit') }}">
        @csrf
        <input type="hidden" name="mobile" value="{{ $mobile }}" />
        <input type="text" name="otp" placeholder="One Time Password" required maxlength="6" />
        <button type="submit">Verify</button>
    </form>
</div>
</body>
</html>
