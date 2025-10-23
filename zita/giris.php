<?php
// Session kontrolü - zaten giriş yapmışsa ana sayfaya yönlendir
session_start();

if (isset($_SESSION['kullanici_id']) && isset($_SESSION['firma_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html> 
<html lang="tr" dir="ltr" data-nav-layout="vertical" data-vertical-style="overlay" data-theme-mode="dark" data-header-styles="dark" data-menu-styles="dark" data-toggled="close">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>

        <!-- Meta Data -->
		<meta charset="UTF-8">
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="Description" content="Zita - Dijital Dönüşüm Platformu">
        <meta name="Author" content="Zita Projesi">
        <meta name="keywords" content="zita, dijital dönüşüm, ziyaretçi takibi, araç takibi, firma yönetimi">
        
        <!-- TITLE -->
		<title>Zita - Giriş Yap</title>

        <!-- FAVICON -->
        <link rel="icon" href="varliklar/images/brand-logos/fav.ico" type="image/x-icon">

        <!-- BOOTSTRAP CSS -->
        <link  id="style" href="varliklar/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- ICONS CSS -->
        <link href="varliklar/css/icons.css" rel="stylesheet">

        <!-- STYLES CSS -->
        <link href="varliklar/css/styles.css" rel="stylesheet">

        <!-- MAIN JS -->
        <script src="varliklar/js/authentication-main.js"></script>


	</head>

    <body>

        
        <div class="page error-bg" id="particles-js">
            <!-- Start::error-page -->
            <div class="error-page  ">
                <div class="container">
                    <div class="row justify-content-center align-items-center authentication authentication-basic h-100">
                        <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-6 col-sm-8 col-12">
                            <div class="my-5 d-flex justify-content-center">
                                <a href="index.html">
                                    <img src="varliklar/images/brand-logos/desktop-logo.png" alt="logo" class="desktop-logo">
                                    <img src="varliklar/images/brand-logos/desktop-dark.png" alt="logo" class="desktop-dark">
                                </a>
                            </div>
                            <div class="card custom-card rectangle2">
                                <div class="card-body p-5 rectangle3">
                                    <p class="h4 fw-semibold mb-2 text-center">Giriş Yap</p>
                                    <p class="mb-4 text-muted op-7 fw-normal text-center">Hoş geldiniz!</p>
                                    <div class="row gy-3">
                                        <div class="col-xl-12">
                                            <label for="signin-username" class="form-label text-default">Kullanıcı Adı</label>
                                            <input type="text" class="form-control form-control-lg" id="signin-username" placeholder="kullanıcı adı">
                                        </div>
                                        <div class="col-xl-12 mb-2">
                                            <label for="signin-password" class="form-label text-default d-block">Şifre<a href="reset-password-basic.html" class="float-end text-primary">Şifremi unuttum?</a></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control form-control-lg" id="signin-password" placeholder="şifre">
                                                <button class="btn btn-light bg-transparent" type="button" onclick="createpassword('signin-password',this)" id="button-addon2"><i class="ri-eye-off-line align-middle"></i></button>
                                            </div>
                                            <div class="mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                                    <label class="form-check-label text-muted fw-normal" for="defaultCheck1">
                                                        Beni hatırla
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-12 d-grid mt-2">
                                            <button type="button" class="btn btn-lg btn-primary" id="girisBtn">Giriş Yap</button>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <p class="fs-12 text-muted mt-3">Hesabınız yok mu? <a href="signup-basic.html" class="text-primary">Kayıt Ol</a></p>
                                    </div>
                                    <div class="text-center my-3 authentication-barrier">
                                        <span>VEYA</span>
                                    </div>
                                    <div class="btn-list text-center">
                                        <button class="btn btn-icon btn-light btn-wave waves-effect waves-light">
                                            <i class="ri-facebook-line fw-bold "></i>
                                        </button>
                                        <button class="btn btn-icon btn-light  btn-wave waves-effect waves-light">
                                            <i class="ri-google-line fw-bold "></i>
                                        </button>
                                        <button class="btn btn-icon btn-light  btn-wave waves-effect waves-light">
                                            <i class="ri-twitter-line fw-bold "></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End::error-page -->
        </div>

        
        <!-- SCRIPTS -->

        <!-- BOOTSTRAP JS -->
        <script src="varliklar/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

        
        <!-- SHOW PASSWORD JS -->
        <script src="varliklar/js/show-password.js"></script>
        
        <!-- CUSTOM JS -->
        <script src="varliklar/js/giris.js"></script>

        <!-- END SCRIPTS -->

	</body>
</html>
