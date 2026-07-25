@props(['items'])
<nav class="public-breadcrumb" aria-label="Breadcrumb">
    <ol>
        @foreach ($items as $item)
            <li class="{{ $loop->last ? 'active' : '' }}">
                @if (!empty($item['url']) && !$loop->last)
                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                @else
                    <span>{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
