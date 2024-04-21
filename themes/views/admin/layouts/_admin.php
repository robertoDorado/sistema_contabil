<?php $v->layout("admin/layouts/_scripts") ?>
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= url("/admin/logout") ?>" id="logout" class="nav-link">Sair</a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="<?= url("/admin") ?>" class="brand-link text-center">
            <span class="brand-text font-weight-light">Sistema Contabil</span><br />
            <span class="right badge badge-danger"><?= (!empty(session()->user->subscription) &&
                                                        session()->user->subscription == "active" ? "Assinatura ativa" : "Conta grátis") ?></span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex text-center">
                <a href="<?= url("/admin") ?>" class="d-block" style="margin:0 auto">Bem vindo <?= $userFullName ?></a>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2" sidebarMenu>
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-dollar-sign"></i>
                            <p>
                                Fluxo de Caixa
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= url("admin/cash-flow-group/form") ?>" class="nav-link">
                                    <p>Lançar grupo de contas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= url("admin/cash-flow-group/report") ?>" class="nav-link">
                                    <p>Relatório grupo de contas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= url("admin/cash-flow/form") ?>" class="nav-link">
                                    <p>Lançar nova conta</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= url("admin/cash-flow/report") ?>" class="nav-link">
                                    <p>Relatório de contas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= url("admin/cash-flow-group/backup/report") ?>" class="nav-link">
                                    <p>Backup grupo de contas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= url("admin/cash-flow/backup/report") ?>" class="nav-link">
                                    <p>Backup de contas</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa-solid fa-user"></i>
                            <p>
                                Cliente
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= url("/admin/customer/update-data/form") ?>" class="nav-link">
                                    <p>Formulário do cliente</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <p>Comprar assinatura mensal</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <p>Cancelar assinatura</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <?= $v->section("content") ?>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
        <div class="p-3">
            <h5>Title</h5>
            <p>Sidebar content</p>
        </div>
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <!-- To the right -->
        <div class="float-right d-none d-sm-inline">
            Sistema para gestão financeira
        </div>
        <!-- Default to the left -->
        <strong>Copyright &copy; 2024-<?= date('Y') ?> <a href="<?= url("/admin") ?>">Laborcode</a>.</strong> Todos os direitos reservados.
    </footer>
</div>
<div endpoints='<?= json_encode($endpoints) ?>' style="display:none"></div>
<!-- ./wrapper -->