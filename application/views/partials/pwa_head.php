<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="manifest" href="<?= base_url('collection/manifest.webmanifest') ?>">
<meta name="theme-color" content="#0A58CA">
<!-- iOS support -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<link rel="apple-touch-icon" href="<?= base_url('collection/icons/icon-192.png') ?>">
<script>
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('<?= base_url('collection/sw.js') ?>', { scope: '/collection/' })
    .catch(console.error);
}
</script>
