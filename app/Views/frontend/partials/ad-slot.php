<?php
$key = (string) ($key ?? '');
$placements = $adPlacements ?? [];
$placement = $placement ?? ($placements[$key] ?? null);

if (!is_array($placement) || empty($placement['is_enabled'])) {
    return;
}

$client = trim((string) ($placement['adsense_client'] ?? ''));
$slot = trim((string) ($placement['adsense_slot'] ?? ''));
$height = max(90, (int) ($placement['reserved_height'] ?? 280));
$format = (string) ($placement['ad_format'] ?? 'auto');

if ($client === '' || $slot === '') {
    return;
}
?>
<aside
    class="ad-slot"
    style="min-height: <?php echo $height; ?>px"
    aria-label="Advertisement"
    data-ad-slot
    data-ad-client="<?php echo $this->escape($client); ?>"
    data-ad-format="<?php echo $this->escape($format); ?>"
>
    <p class="ad-slot__label">Advertisement</p>
    <ins
        class="adsbygoogle"
        style="display:block"
        data-ad-client="<?php echo $this->escape($client); ?>"
        data-ad-slot="<?php echo $this->escape($slot); ?>"
        data-ad-format="<?php echo $this->escape($format); ?>"
        data-full-width-responsive="true"
    ></ins>
</aside>
