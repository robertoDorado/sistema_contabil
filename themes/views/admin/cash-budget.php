<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatório de orçamento de caixa</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Início</a></li>
                        <li class="breadcrumb-item active">Relatório de orçamento de caixa</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 mb-5">
                    <?= $v->insert("admin/layouts/_daterange_input") ?>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Relatório de orçamento de caixa</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="cashFlowBudget" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Descrição</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>(A) Mês correspondente</strong></td>
                                            <td><?= $cashBudget["month"] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>(B) Saldo de caixa inicial</strong></td>
                                            <td><strong><?= "R$ " . number_format($cashBudget["opening_cash_balance"], 2, ",", ".") ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>(C) Entradas de caixa</strong></td>
                                            <td>-</td>
                                        </tr>
                                        <?php if (!empty($cashReceipts)) : ?>
                                            <?php foreach ($cashReceipts as $receipts) : ?>
                                                <tr>
                                                    <td>(D) -<?= $receipts["group_name"] ?></td>
                                                    <td>(D) <?= $receipts["total_entry"] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <tr>
                                            <td><strong>(E) Total de entradas</strong></td>
                                            <td><strong><?= $totalReceipts ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>(F) Sádas de caixa</strong></td>
                                            <td>-</td>
                                        </tr>
                                        <?php if (!empty($cashOutflows)) : ?>
                                            <?php foreach ($cashOutflows as $outFlows) : ?>
                                                <tr>
                                                    <td>(G) -<?= $outFlows["group_name"] ?></td>
                                                    <td>(G) <?= $outFlows["total_entry"] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <tr>
                                            <td><strong>(H) Total de saídas</strong></td>
                                            <td><strong><?= $totalOutflows ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>(I) Saldo de caixa final</strong></td>
                                            <td><strong><?= "R$ " . number_format($finalCashBalance, 2, ",", ".") ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>