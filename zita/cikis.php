<?php
/**
 * Zita Projesi - Çıkış Sayfası
 * 
 * Session'ı güvenli şekilde sonlandırır ve giriş sayfasına yönlendirir
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Session başlat
session_start();

// CSRF token kontrolü (güvenlik için)
if (!isset($_GET['token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_GET['token'] ?? '')) {
    // Token yoksa veya eşleşmiyorsa güvenli çıkış yap
    session_destroy();
    header('Location: giris.php?cikis=guvenli');
    exit();
}

// Session verilerini temizle
$_SESSION = array();

// Session cookie'sini sil
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Session'ı tamamen sonlandır
session_destroy();

// Cache kontrolü - sayfa cache'lenmesin
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Güvenli yönlendirme
header('Location: giris.php?cikis=basarili');
exit();
?>
