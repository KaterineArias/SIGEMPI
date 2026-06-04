<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGEMPI — @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    @yield('body')
    @yield('content')
    <script>
    (function(){
        const t = document.querySelector('[data-theme-toggle]');
        const r = document.documentElement;
        let d = localStorage.getItem('theme') || 'light';
        r.setAttribute('data-theme', d);
        if (t) {
            t.addEventListener('click', () => {
                d = d === 'dark' ? 'light' : 'dark';
                r.setAttribute('data-theme', d);
                localStorage.setItem('theme', d);
                t.innerHTML = d === 'dark'
                    ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>'
                    : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
            });
        }
    })();
    </script>
    @stack('scripts')
</body>
</html>