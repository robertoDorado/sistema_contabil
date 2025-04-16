<?php
$verifyEndpoints = ["/admin/login", "/customer/subscribe", "/customer/subscription/thanks-purchase"];
$defineClass = in_array($_SERVER["REDIRECT_URL"], $verifyEndpoints) ? "login-page" : "sidebar-mini";
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
    <title>Sistema Financeiro</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?= url(CONF_ADMIN_PATH . "/plugins/fontawesome-free/css/all.min.css") ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= url(CONF_ADMIN_PATH . "/dist/css/adminlte.min.css") ?>">
    <!-- Favicon -->
    <link rel="icon" href="<?= theme("/assets/img/icons8-contabilidade-48.png") ?>" type="image/png">
    <!-- jQuery -->
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/jquery/jquery.min.js") ?>"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <!-- DataTables -->
    <link rel="stylesheet" href="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css") ?>">
    <link rel="stylesheet" href="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-responsive/css/responsive.bootstrap4.min.css") ?>">
    <link rel="stylesheet" href="<?= url(CONF_ADMIN_PATH . "/plugins/datatables-buttons/css/buttons.bootstrap4.min.css") ?>">
    <!-- Date Picker -->
    <link rel="stylesheet" href="<?= url(CONF_ADMIN_PATH . "/plugins/daterangepicker/daterangepicker.css") ?>">
    <!-- Chartjs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Arquivo JS do Datepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <!-- Arquivo de localização do Datepicker (opcional) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.pt-BR.min.js"></script>
    <!-- Arquivo CSS do Datepicker -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <!-- Laborcode css -->
    <link rel="stylesheet" href="<?= theme("assets/style.css") ?>">
    <!-- Stripe JS -->
    <script src="https://js.stripe.com/v3/"></script>
    <!-- Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-17009805739"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'AW-17009805739');
    </script>
</head>

<body class="hold-transition <?= $defineClass ?>">
    <?= $v->section("content") ?>
    <div id="urlJson" style="display:none" data-url="<?= theme("assets/datatable/datatable.json") ?>"></div>

    <!-- REQUIRED SCRIPTS -->

    <!-- Bootstrap 4 -->
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>
    <!-- Form Bootstrap Wizard -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-bootstrap-wizard@1.4.2/jquery.bootstrap.wizard.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?= url(CONF_ADMIN_PATH . "/dist/js/adminlte.min.js") ?>"></script>
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
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/bootstrap/js/bootstrap.min.js") ?>"></script>
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/moment/moment.min.js") ?>"></script>
    <script src="<?= url(CONF_ADMIN_PATH . "/plugins/daterangepicker/daterangepicker.js") ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- Laborcode Scripts -->
    <script src="<?= theme("/assets/scripts.js") ?>"></script>
</body>

</html>