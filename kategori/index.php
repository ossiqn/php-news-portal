<?php
require_once __DIR__ . '/../functions.php';
$catSlug = trim($_GET['slug'] ?? '');
if (!$catSlug) { header('Location: /'); exit; }
$cat = getCategoryBySlug($catSlug);
if ($cat) { $catName = $cat['name']; $catColor = $cat['color']; } else { $catName = ucfirst(str_replace(['-','_'], ' ', $catSlug)); $catColor = '#c8102e'; }
require_once __DIR__ . '/_template.php';
