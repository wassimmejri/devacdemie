<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<nav class="main-navigation">
  <?php
  wp_nav_menu([
    'theme_location' => 'menu-lateral',
    'container'      => false,
    'menu_class'     => 'main-menu',
    // Retire fallback_cb => false
    // ou pour un fallback automatique :
    'fallback_cb'    => 'wp_page_menu',
  ]);
  ?>
</nav>



<!-- Début de contenu principal -->
<div id="content" class="site-content">
