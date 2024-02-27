<?php

use PharIo\Manifest\Url;

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
    <title>Sistema Contabil</title>

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
    <!-- DataTables -->
    <link rel="stylesheet" href="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css") ?>">
    <link rel="stylesheet" href="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-responsive/css/responsive.bootstrap4.min.css") ?>">
    <link rel="stylesheet" href="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-buttons/css/buttons.bootstrap4.min.css") ?>">
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
    <!-- DataTables -->
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/datatables/jquery.dataTables.min.js") ?>"></script>
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js") ?>"></script>
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-responsive/js/dataTables.responsive.min.js") ?>"></script>
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-responsive/js/responsive.bootstrap4.min.js") ?>"></script>
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-buttons/js/dataTables.buttons.min.js") ?>"></script>
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-buttons/js/buttons.bootstrap4.min.js") ?>"></script>
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-buttons/js/buttons.html5.min.js") ?>"></script>
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-buttons/js/buttons.print.min.js") ?>"></script>
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-buttons/js/buttons.colVis.min.js") ?>"></script>
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/jszip/jszip.min.js") ?>"></script>
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/pdfmake/pdfmake.min.js") ?>"></script>
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/pdfmake/vfs_fonts.js") ?>"></script>
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-buttons/js/buttons.html5.min.js") ?>"></script>
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-buttons/js/buttons.print.min.js") ?>"></script>
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-buttons/js/buttons.colVis.min.js") ?>"></script>
</body>

</html>