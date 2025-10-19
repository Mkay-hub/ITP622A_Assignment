<?php
require_once __DIR__ . '/includes/config.php';

$stmt = $pdo->query("SELECT NOW() AS now");
$row = $stmt->fetch();

echo "✅ Connected to LightCast DB successfully at: " . $row['now'];
