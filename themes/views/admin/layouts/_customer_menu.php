<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-money-check"></i>
            <p>
                Menu fiscal
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <p>Nota Fiscal Eletrônica</p>
                    <i class="right fas fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?= url("/admin/invoice/form") ?>" class="nav-link">
                            <p>Emissão de NF-e</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url("/admin/invoice/report") ?>" class="nav-link">
                            <p>Relatório de NF-e</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url("/admin/invoice/backup") ?>" class="nav-link">
                            <p>Backup de NF-e</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <p>Parametrização Fiscal</p>
                    <i class="right fas fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?= url("/admin/tax-regime/form") ?>" class="nav-link">
                            <p>Regime Tributário</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
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
                    <p>Relatórios contábeis</p>
                    <i class="right fas fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?= url("/admin/balance-sheet/daily-journal/report") ?>" class="nav-link">
                            <p>Livro diário</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url("/admin/balance-sheet/daily-journal/report/backup") ?>" class="nav-link">
                            <p>Backup do livro diário</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url("/admin/balance-sheet/trial-balance/report") ?>" class="nav-link">
                            <p>Balancete de verificação</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url("/admin/balance-sheet/general-ledge/report") ?>" class="nav-link">
                            <p>Livro razão</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url("/admin/balance-sheet/income-statement/report") ?>" class="nav-link">
                            <p>DRE</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url("/admin/balance-sheet/statement-of-value-added/report") ?>" class="nav-link">
                            <p>DVA</p>
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
                        <a href="<?= url("/admin/balance-sheet-explanatory-notes/form") ?>" class="nav-link">
                            <p>Adicionar nova nota</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url("/admin/balance-sheet-explanatory-notes/report") ?>" class="nav-link">
                            <p>Relatório de notas</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url("/admin/balance-sheet-explanatory-notes/form/backup") ?>" class="nav-link">
                            <p>Backup de notas</p>
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
            <?php if (!in_array(session()->user->subscription, ['active', 'trialing'])) : ?>
                <li class="nav-item">
                    <a href="<?= url(DEFAULT_ENDPOINT_SUBSCRIPTION) ?>" class="nav-link">
                        <p>Comprar assinatura mensal</p>
                    </a>
                </li>
            <?php endif ?>
            <?php if (!empty(session()->user->subscription) && in_array(session()->user->subscription, ['active', 'trialing'])) : ?>
                <li class="nav-item">
                    <a href="<?= url("/admin/customer/cancel-subscription") ?>" class="nav-link">
                        <p>Cancelar assinatura</p>
                    </a>
                </li>
            <?php endif ?>
        </ul>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-history"></i>
            <p>
                Auditoria e Históricos
                <i class="right fas fa-angle-left"></i>
            </p>
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
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-headset"></i>
            <p>
                Chamados
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="<?= url("/admin/support/open/ticket") ?>" class="nav-link">
                    <p>Abrir um chamado</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= url("/admin/support/my-tickets") ?>" class="nav-link">
                    <p>Meus chamados</p>
                </a>
            </li>
        </ul>
    </li>
</ul>