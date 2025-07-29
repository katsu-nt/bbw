<!DOCTYPE html>
<html xmlns="https://www.w3.org/1999/xhtml" lang="vi">

<head>
    <meta name="MobileOptimized" content="device-width" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1.0, user-scalable=no">
    <title>{{ $metadata['title'] ?? 'Bloomberg Businessweek VietNam' }}</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" />
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="author" content="{{ $metadata['author'] ?? 'Bloomberg Businessweek VietNam' }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="HandheldFriendly" content="true" />
    <meta name="google" content="notranslate">
    <meta name="robots" content="noodp,index,follow" />
    <meta name="keywords" content="{{ $metadata['keywords'] ?? 'Bloomberg Businessweek VietNam' }}" />
    <meta name="description" content="{{ $metadata['description'] ?? 'Góc nhìn toàn cảnh - Dữ liệu toàn cầu – Phân tích & dự báo' }}" />
    <meta property="fb:pages" content="" />
    <link rel="canonical" href="{{ $metadata['og_url'] ?? url('/') }}" />
    <meta property="og:title" content="{{ $metadata['title'] ?? 'Bloomberg Businessweek VietNam' }}">
    <meta property="og:description" content="{{ $metadata['description'] ?? 'Góc nhìn toàn cảnh - Dữ liệu toàn cầu – Phân tích & dự báo' }}" />
    <meta property="og:image" content="{{ $metadata['og_image'] ?? asset('images/bbw.jpg') }}" />
    <meta property="og:url" content="{{ $metadata['og_url'] ?? url('/') }}" />
    <meta property="og:site_name" content="bbw.vn" />
    <meta property="og:type" content="website" />
    <meta property="og:locale" itemprop="inLanguage" content="vi_VN" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Styles -->
    <link href="{{ asset('css/style.css') }}?v=1" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}?v=3.3" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v=3.3" rel="stylesheet">
    @if (Request::routeIs('article.show'))
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Arima+Madurai:wght@400;700&family=Bangers&family=Barlow:ital,wght@0,400;0,700;1,400;1,700&family=Bitter:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&family=Bungee+Inline&family=Chakra+Petch:ital,wght@0,400;0,700;1,400;1,700&family=Comforter&family=Cormorant+Garamond:ital,wght@0,400;0,700;1,400;1,700&family=Cormorant+Infant:ital,wght@0,400;0,700;1,400;1,700&family=Dancing+Script:wght@400;700&family=EB+Garamond:ital,wght@0,400;0,700;1,400;1,700&family=Fuzzy+Bubbles:wght@400;700&family=Grandstander:ital,wght@0,400;0,700;1,400;1,700&family=Great+Vibes&family=IBM+Plex+Serif:ital,wght@0,400;0,700;1,400;1,700&family=Ingrid+Darling&family=Inter:wght@400;700&family=Itim&family=Kanit:ital,wght@0,400;0,700;1,400;1,700&family=Kodchasan:ital,wght@0,400;0,700;1,400;1,700&family=Lavishly+Yours&family=Lemonada:wght@400;700&family=Lobster&family=Lora:ital,wght@0,400;0,700;1,400;1,700&family=Mali:ital,wght@0,400;0,700;1,400;1,700&family=Meow+Script&family=Montserrat:ital,wght@0,400;0,700;1,400;1,700&family=Moo+Lah+Lah&family=Moon+Dance&family=Neonderthaw&family=Noto+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Nunito:ital,wght@0,400;0,700;1,400;1,700&family=Ole&family=Open+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Pacifico&family=Pangolin&family=Patrick+Hand&family=Paytone+One&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&family=Potta+One&family=Praise&family=Raleway:ital,wght@0,400;0,700;1,400;1,700&family=Roboto+Condensed:ital,wght@0,400;0,700;1,400;1,700&family=Roboto+Serif:ital,opsz,wght@0,8..144,400;0,8..144,700;1,8..144,400;1,8..144,700&family=Roboto:ital,wght@0,400;0,700;1,400;1,700&family=Saira+Stencil+One&family=Sansita+Swashed:wght@400;700&family=Send+Flowers&family=Signika:wght@400;700&family=Smooch+Sans:wght@400;700&family=Source+Sans+Pro:ital,wght@0,400;0,700;1,400;1,700&family=Splash&family=The+Nautigal:wght@400;700&family=Tinos:ital,wght@0,400;0,700;1,400;1,700&family=Tourney:ital,wght@0,400;0,700;1,400;1,700&family=Twinkle+Star&family=VT323&family=Vollkorn:ital,wght@0,400;0,700;1,400;1,700&family=Vujahday+Script&family=Work+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Yeseva+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://asset.1cdn.vn/onecms/all/editor/snippets-custom.bundle.min.css?t=20240620">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- GOOGLE SEARCH META GOOGLE SEARCH STRUCTURED DATA FOR ARTICLE && GOOGLE BREADCRUMB STRUCTURED DATA-->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "bbw.vn",
            "alternateName": "Góc nhìn toàn cảnh - Dữ liệu toàn cầu - Phân tích & dự báo",
            "url": "https://bbw.vn"
        }
    </script>
    @stack('head')
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-M2Q6FPH');
    </script>
    <!-- End Google Tag Manager -->
</head>

<body>

    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M2Q6FPH"
            height="0" width="0" style="display:none;visibility:hidden">
        </iframe>
    </noscript>
    <!-- End Google Tag Manager -->

    <!-- Your Page Content -->
    @include('layouts.header')
    @yield('content')
    @include('layouts.footer')
    @stack('scripts')
</body>

</html>