@if($siteAd ?? null)
<aside class="v2-sponsored" aria-label="Sponsored advertisement"><a href="{{ $siteAd->link_url }}" target="_blank" rel="noopener sponsored">@if($siteAd->media_type === 'video')<video src="{{ $siteAd->media_source }}" autoplay muted loop playsinline controls></video>@elseif($siteAd->media_source)<img src="{{ $siteAd->media_source }}" alt="{{ $siteAd->title }}">@endif<span>Sponsored · {{ $siteAd->title }}</span></a></aside>
@endif
