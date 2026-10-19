@if($showWhatsappButton ?? false)
<a class="v2-whatsapp" href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsappNumber) }}" target="_blank" rel="noopener" aria-label="Chat with Rivex.uk on WhatsApp" data-legacy-label="Chat with DVSE on WhatsApp">WhatsApp</a>
@endif
