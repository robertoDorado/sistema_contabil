<?php $v->layout("admin/layouts/_scripts") ?>
<div class="wrapper">
    <?php $v->insert("admin/layouts/_modal_loader") ?>
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
                                                        session()->user->subscription == "active" ? "Assinatura ativa" : "Conta grátis") ?></span><br />
            <?php if (!empty(session()->user->period_end) && session()->user->subscription == "active") : ?>
                <span style="font-size:1rem" class="brand-text font-weight-light">Renovação em <?= date("d/m/Y", strtotime(session()->user->period_end)) ?></span>
            <?php endif ?>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex text-center">
                <a href="<?= url("/admin") ?>" class="d-block" style="margin:0 auto">Bem vindo <?= $userFullName ?></a>
            </div>

            <?php if (userHasCompany()) : ?>
                <div class="container-company">
                    <div class="form-group">
                        <select class="form-control" name="selectCompanySession" id="selectCompanySession">
                            <option value="" disabled selected>Selecione uma empresa</option>
                            <?php foreach (getCompanysNameByUserId() as $company) : ?>
                                <?php if (empty($company->getDeleted())) : ?>
                                    <option value="<?= $company->id ?>" <?= !empty(session()->user->company_id) && session()->user->company_id == $company->id ? "selected" : "" ?>><?= $company->company_name ?></option>
                                <?php endif ?>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            <?php endif ?>

            <!-- Sidebar Menu -->
            <nav class="mt-2" sidebarMenu>
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-balance-scale"></i>
                            <p>
                                Balanço patrimonial
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= url("/admin/balance-sheet/balance-sheet-overview/report") ?>" class="nav-link">
                                    <p>Visão geral do balanço</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <p>Configuração do balanço</p>
                                    <i class="right fas fa-angle-left"></i>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/balance-sheet/chart-of-account") ?>" class="nav-link">
                                            <p>Plano de contas</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/balance-sheet/chart-of-account/backup") ?>" class="nav-link">
                                            <p>Backup plano de contas</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/balance-sheet/chart-of-account-group/form") ?>" class="nav-link">
                                            <p>Nova categoria de contas</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/balance-sheet/chart-of-account-group") ?>" class="nav-link">
                                            <p>Categoria de contas</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/balance-sheet/chart-of-account-group/backup") ?>" class="nav-link">
                                            <p>Backup categoria de contas</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="<?= url("/admin/balance-sheet/balance-sheet-overview/form") ?>" class="nav-link">
                                    <p>Lançamentos contábeis</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <p>Conciliação bancária</p>
                                    <i class="right fas fa-angle-left"></i>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <p>Conciliação automática</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <p>Conciliação manual</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <p>Relatórios contábeis</p>
                                    <i class="right fas fa-angle-left"></i>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/balance-sheet/daily-journal/report") ?>" class="nav-link">
                                            <p>Livro diário</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <p>Notas explicativas</p>
                                    <i class="right fas fa-angle-left"></i>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <p>Adicionar notas</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <p>Visualizar notas</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <p>Auditoria e Históricos</p>
                                    <i class="right fas fa-angle-left"></i>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <p>Registros de auditoria</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <p>Relatórios de auditoria</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-building-user"></i>
                            <p>
                                Empresas
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= url("admin/company/register") ?>" class="nav-link">
                                    <p>Criar nova empresa</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= url("admin/company/report") ?>" class="nav-link">
                                    <p>Relatório de empresas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= url("/admin/company/backup/report") ?>" class="nav-link">
                                    <p>Backup de empresas</p>
                                </a>
                            </li>
                        </ul>
                    </li>
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
                                <a href="<?= url("admin/cash-flow/report") ?>" class="nav-link">
                                    <p>Visão Geral do fluxo de caixa</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <p>Config. do fluxo de caixa</p>
                                    <i class="right fas fa-angle-left"></i>
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
                                        <a href="<?= url("admin/cash-flow-group/backup/report") ?>" class="nav-link">
                                            <p>Backup grupo de contas</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="<?= url("admin/cash-flow/form") ?>" class="nav-link">
                                    <p>Lançamentos de caixa</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= url("admin/cash-flow/backup/report") ?>" class="nav-link">
                                    <p>Backup de contas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <p>Conciliação bancária</p>
                                    <i class="right fas fa-angle-left"></i>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/bank-reconciliation/cash-flow/automatic") ?>" class="nav-link">
                                            <p>Conciliação automática</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/bank-reconciliation/cash-flow/manual") ?>" class="nav-link">
                                            <p>Conciliação manual</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <p>Análises e indicadores</p>
                                    <i class="right fas fa-angle-left"></i>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/analyzes-and-indicators/cash-flow/cash-flow-projections") ?>" class="nav-link">
                                            <p>Projeções de fluxo de caixa</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/analyzes-and-indicators/cash-flow/financial-indicators") ?>" class="nav-link">
                                            <p>Indicadores financeiros</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/analyzes-and-indicators/cash-flow/charts-and-visualizations") ?>" class="nav-link">
                                            <p>Gráficos e visualizações</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <p>Config. variação de caixa</p>
                                    <i class="right fas fa-angle-left"></i>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/cash-variation-setting/form") ?>" class="nav-link">
                                            <p>Lançar variação de caixa</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/cash-variation-setting/report") ?>" class="nav-link">
                                            <p>Relatório variação de caixa</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/cash-variation-setting/backup") ?>" class="nav-link">
                                            <p>Backup variação de caixa</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <p>Planejamento de caixa</p>
                                    <i class="right fas fa-angle-left"></i>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/cash-planning/cash-flow/cash-budget") ?>" class="nav-link">
                                            <p>Orçamento de caixa</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/cash-planning/cash-flow/cash-variation-analysis") ?>" class="nav-link">
                                            <p>Análise de variação caixa</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <p>Notas explicativas</p>
                                    <i class="right fas fa-angle-left"></i>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/cash-flow-explanatory-notes/form") ?>" class="nav-link">
                                            <p>Adicionar nova nota</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/cash-flow-explanatory-notes/report") ?>" class="nav-link">
                                            <p>Relatório de notas</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/cash-flow-explanatory-notes/backup") ?>" class="nav-link">
                                            <p>Backup de notas</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <p>Auditoria e Históricos</p>
                                    <i class="right fas fa-angle-left"></i>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/history-audit/report") ?>" class="nav-link">
                                            <p>Relatório da auditoria</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= url("/admin/history-audit/backup") ?>" class="nav-link">
                                            <p>Backup da auditoria</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa-solid fa-user"></i>
                            <p>
                                Usuário
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= url("/admin/customer/update-data/form") ?>" class="nav-link">
                                    <p>Formulário do usuário</p>
                                </a>
                            </li>
                            <?php if (session()->user->subscription != "active") : ?>
                                <li class="nav-item">
                                    <a href="<?= url("/customer/subscribe") ?>" class="nav-link">
                                        <p>Comprar assinatura mensal</p>
                                    </a>
                                </li>
                            <?php endif ?>
                            <?php if (!empty(session()->user->subscription) && session()->user->subscription == "active") : ?>
                                <li class="nav-item">
                                    <a href="<?= url("/admin/customer/cancel-subscription") ?>" class="nav-link">
                                        <p>Cancelar assinatura</p>
                                    </a>
                                </li>
                            <?php endif ?>
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