<?php
$site = $site ?? [];
$phone = (string) ($site['phone'] ?? '+2340000000000');
$whatsapp = (string) ($site['whatsapp'] ?? '2340000000000');
?>
<!-- Spacer so the fixed bar never covers footer content on mobile -->
<div class="h-16 lg:hidden" aria-hidden="true"></div>

<div class="mobile-action-bar" role="navigation" aria-label="Quick actions">
    <a href="tel:<?php echo $this->escape($phone); ?>" class="mobile-action-bar__item" data-analytics-event="mobile_call">
        <span class="text-desnky-primary"><?php echo $this->partial('frontend/partials/icon', ['name' => 'phone', 'class' => 'h-5 w-5']); ?></span>
        Call
    </a>
    <a href="https://wa.me/<?php echo $this->escape($whatsapp); ?>" class="mobile-action-bar__item" rel="noopener" target="_blank" data-analytics-event="mobile_whatsapp">
        <span class="text-desnky-secondary"><?php echo $this->partial('frontend/partials/icon', ['name' => 'whatsapp', 'class' => 'h-5 w-5']); ?></span>
        WhatsApp
    </a>
    <a href="/contact" class="mobile-action-bar__item bg-desnky-primary text-white" data-analytics-event="mobile_quote">
        <?php echo $this->partial('frontend/partials/icon', ['name' => 'mail', 'class' => 'h-5 w-5']); ?>
        Get a Quote
    </a>
</div>
