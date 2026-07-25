@if ($site['whatsapp'])
    @php($whatsappUrl = 'https://wa.me/'.$site['whatsapp'].'?text='.urlencode($site['whatsapp_message']))
    <a class="whatsapp-floating" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Contact {{ $site['short_name'] }} through WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>
@endif
