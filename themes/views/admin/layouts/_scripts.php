<?php
$defineClass = preg_match("/login/", $_SERVER["REDIRECT_URL"]) ? "login-page" : "sidebar-mini";
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Starter</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?= url(CONF_ADMIN_PATH . "/plugins/fontawesome-free/css/all.min.css") ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= url(CONF_ADMIN_PATH . "/dist/css/adminlte.min.css") ?>">
    <!-- Favicon -->
    <link rel="icon" href="<?= theme("/assets/img/icons8-contabilidade-48.png") ?>" type="image/png">
    <!-- jQuery -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<!-- Toastr -->
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
</head>

<body class="hold-transition <?= $defineClass ?>">
    <?= $v->section("content") ?>

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <!-- <script src="<?= url(CONF_ADMIN_PATH . "/plugins/jquery/jquery.min.js") ?>"></script> -->
    <!-- Bootstrap 4 -->
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?= url(CONF_ADMIN_PATH . "/dist/js/adminlte.min.js") ?>"></script>
    <!-- Laborcode Scripts -->
    <script src="<?= theme("/assets/scripts.js") ?>"></script>
</body>

</html>