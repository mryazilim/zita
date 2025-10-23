<?php
require_once __DIR__ . '/../ortak/veritabani/baglanti.php';

$db = veritabani_baglanti();

echo "Firmalar tablosu:\n";
$result = $db->get_results('DESCRIBE firmalar');
foreach($result as $row) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}

echo "\nFirma kullanıcıları tablosu:\n";
$result = $db->get_results('DESCRIBE firma_kullanicilari');
foreach($result as $row) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}
?>
