<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Splash</title>
    <style>
        body, html {margin:0; padding:0; height:100%; display:flex; align-items:center; justify-content:center; background:#0d0d0d; color:#fff; font-family:'Inter',sans-serif;}
        .logo {font-size:2rem; margin-bottom:1rem;}
        .spinner {border:4px solid rgba(255,255,255,0.2); border-top:4px solid #fff; border-radius:50%; width:60px; height:60px; animation:spin 1s linear infinite;}
        @keyframes spin {to {transform:rotate(360deg);}}
    </style>
    <script>
        setTimeout(()=>{window.location.href='{{ route('auth.enter_mobile') }}';},2000);
    </script>
</head>
<body>
    <div class="logo">MyShift App</div>
    <div class="spinner"></div>
</body>
</html>
