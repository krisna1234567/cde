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
            <div class="footer-contact">
                @if (filled($site['address']))
                    <div class="footer-contact-item footer-address" title="{{ $site['address'] }}" style="flex: 0 1 300px; width: 100%; max-width: 300px; min-width: 0; text-align: left;">
                        <i class="bi bi-geo-alt" aria-hidden="true"></i>
                        <span style="display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; overflow: hidden; white-space: normal; overflow-wrap: anywhere; min-width: 0; line-height: 1.5;">{{ $site['address'] }}</span>
                    </div>
                @endif
                @if (filled($site['phone']))
                    <a class="footer-contact-item" href="tel:{{ preg_replace('/[^0-9+]/', '', $site['phone']) }}">
                        <i class="bi bi-telephone" aria-hidden="true"></i>
                        <span>{{ $site['phone'] }}</span>
                    </a>
                @endif
                @if (filled($site['email']))
                    <a class="footer-contact-item" href="mailto:{{ $site['email'] }}">
                        <i class="bi bi-envelope" aria-hidden="true"></i>
                        <span>{{ $site['email'] }}</span>
                    </a>
                @endif
                <p class="mb-0 footer-copyright" style="margin-left: auto; flex-shrink: 0; white-space: nowrap; text-align: right;">© {{ date('Y') }} {{ $site['copyright'] }}</p>
            </div>
        </div>
    </div>
</footer>
