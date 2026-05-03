<?php
require_once __DIR__ . '/../config.php';
adminAuth();
header('Location: ' . BASE_PATH . '/admin/reklamlar/');
exit;
