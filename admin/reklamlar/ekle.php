<?php
require_once __DIR__ . '/../config.php';
adminAuth();
header('Location: ' . ADMIN_BASE . '/reklamlar/');
exit;
