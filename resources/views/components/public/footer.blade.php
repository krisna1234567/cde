<footer class="site-footer">
    <div class="container-xl">
        <div class="footer-top">
            <a class="footer-brand" href="{{ route('home') }}" aria-label="{{ $site['name'] }} home">
                <img src="{{ asset($site['logo']) }}" alt="{{ $site['logo_alt'] }}" width="161" height="46" loading="lazy">
            </a>

            <nav class="footer-navigation" aria-label="Footer navigation">
                @foreach ($navigation as $item)
                    @continue($item['route'] === 'home')
                    <a href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                @endforeach
                <a href="{{ route('contact.index') }}">Contact Us</a>
            </nav>

            <div class="footer-socials" aria-label="Social media">
                @foreach ($socials as $social)
                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}"><i class="bi {{ $social['icon'] }}"></i></a>
                @endforeach
            </div>
        </div>

        <div class="footer-divider"></div>

        <div class="footer-bottom">
            <div class="footer-legal">
                <a href="{{ route('privacy') }}">Privacy Policy</a>
                <a href="{{ route('terms') }}">Terms of Use</a>
            </div>
            <p class="mb-0">© {{ date('Y') }} {{ $site['copyright'] }}</p>
        </div>
    </div>
</footer>
