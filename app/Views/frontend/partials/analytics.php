<?php $measurementId = (string) \App\Config::get('seo.analytics.ga_measurement_id', ''); ?>
<?php if ($measurementId !== '') : ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $this->escape($measurementId); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo $this->escape($measurementId); ?>', {
            anonymize_ip: true,
            send_page_view: true
        });
    </script>
<?php endif; ?>
