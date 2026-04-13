<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? html_escape($title) : 'Recruitment' ?></title>
    <?php $this->load->view('includes/common-css-links'); ?>
    <?php if (!empty($extra_css)): ?>
      <?php $css_items = is_array($extra_css) ? $extra_css : [$extra_css]; ?>
      <?php foreach ($css_items as $css): ?>
        <link rel="stylesheet" href="<?= preg_match('#^https?://#i', (string)$css) ? $css : base_url($css) ?>">
      <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
  <section class="home">
    <div class="container">
       <?php $this->load->view('template/top-bar'); ?>
       <?php $this->load->view('template/side-bar'); ?>
    <div class="content col-12 col-md-9">
