<?php
$base = __DIR__ . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'Feature' . DIRECTORY_SEPARATOR . 'Layanan';
$files = glob($base . DIRECTORY_SEPARATOR . 'TC-LAY-*.php');
foreach ($files as $old) {
    $new = preg_replace('/TC-LAY-(\d{2})\.php$/', 'TC_LAY_$1_Test.php', $old);
    if ($new !== $old) {
        rename($old, $new);
        echo basename($old) . " -> " . basename($new) . "\n";
    }
}
