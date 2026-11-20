{{--
    500: kesalahan di server.
    Sengaja TIDAK memakai layout utama: kalau error-nya karena database mati,
    layout (yang mengecek login admin) bisa ikut error. Jadi halaman ini dibuat berdiri sendiri.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ada masalah · Dapurku</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #fffaf3; color: #292524;
               font-family: system-ui, sans-serif; text-align: center; padding: 1rem; }
        h1 { font-family: Georgia, serif; font-size: 2rem; margin: 1rem 0 .5rem; }
        p { color: #57534e; max-width: 28rem; margin: 0 auto; line-height: 1.6; }
        a { display: inline-block; margin-top: 1.5rem; background: #fb5d12; color: #fff; padding: .8rem 1.6rem;
            border-radius: 999px; font-weight: 700; text-decoration: none; }
        .ikon { font-size: 5rem; }
    </style>
</head>
<body>
    <main>
        <div class="ikon">🔥</div>
        <h1>Ups, dapurnya sedang berasap</h1>
        <p>Ada masalah di server kami. Tim dapur sedang memperbaikinya. Coba lagi beberapa saat lagi, ya.</p>
        <a href="{{ url('/') }}">🏠 Ke Beranda</a>
    </main>
</body>
</html>
