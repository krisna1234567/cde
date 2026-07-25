<title>{{ $meta['title'] }}</title>
<meta name="description" content="{{ $meta['description'] }}">
<meta name="robots" content="{{ $meta['robots'] ?? 'index,follow' }}">
<link rel="canonical" href="{{ $meta['canonical'] }}">
<meta property="og:type" content="{{ $meta['type'] ?? 'website' }}">
<meta property="og:title" content="{{ $meta['og_title'] ?? $meta['title'] }}">
<meta property="og:description" content="{{ $meta['og_description'] ?? $meta['description'] }}">
<meta property="og:image" content="{{ $meta['image'] }}">
<meta property="og:url" content="{{ $meta['canonical'] }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $meta['og_title'] ?? $meta['title'] }}">
<meta name="twitter:description" content="{{ $meta['og_description'] ?? $meta['description'] }}">
<meta name="twitter:image" content="{{ $meta['image'] }}">
