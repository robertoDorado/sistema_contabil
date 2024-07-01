<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatório de variação de caixa</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Início</a></li>
                        <li class="breadcrumb-item active">Relatório de variação de caixa</li>
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
                            <h3 class="card-title">Relatório de variação de caixa</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="cashVariationAnalysis" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Descrição</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>(A) Mês correspondente</strong></td>
                                            <td><?= $month ?></td>
                                        </tr>
                                        <?php if (!empty($grouppedOperatingCashFlow)) : ?>
                                            <tr>
                                                <td><strong>(B) Fluxo de caixa operacional</strong></td>
                                                <td>-</td>
                                            </tr>
                                            <?php foreach ($grouppedOperatingCashFlow as $operating) : ?>
                                                <tr>
                                                    <td>(C) -<?= $operating["history"] ?></td>
                                                    <td>(C) <?= $operating["total_entry"] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                            <tr>
                                                <td><strong>(D) Total Fluxo de caixa operacional</strong></td>
                                                <td><strong><?= "R$ " . number_format($totalGrouppedOperatingCashFlow, 2, ",", ".") ?></strong></td>
                                            </tr>
                                        <?php endif ?>
                                        <?php if (!empty($grouppedInvestmentCashFlow)) : ?>
                                            <tr>
                                                <td><strong>(E) Fluxo de caixa de investimentos</strong></td>
                                                <td>-</td>
                                            </tr>
                                            <?php foreach ($grouppedInvestmentCashFlow as $investment) : ?>
                                                <tr>
                                                    <td>(F) -<?= $investment["history"] ?></td>
                                                    <td>(F) <?= $investment["total_entry"] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                            <tr>
                                                <td><strong>(G) Total de fluxo de caixa de investimentos</strong></td>
                                                <td><strong><?= "R$ " . number_format($totalGrouppedInvestmentCashFlow, 2, ",", ".") ?></strong></td>
                                            </tr>
                                        <?php endif ?>
                                        <?php if (!empty($grouppedFinancingCashFlow)) : ?>
                                            <tr>
                                                <td><strong>(H) Fluxo de caixa de financiamento</strong></td>
                                                <td><strong>-</strong></td>
                                            </tr>
                                            <?php foreach($grouppedFinancingCashFlow as $financing): ?>
                                                <tr>
                                                    <td>(I) -<?= $financing["history"] ?></td>
                                                    <td>(I) <?= $financing["total_entry"] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                            <tr>
                                                <td><strong>(J) Total de fluxo de caixa de financiamento</strong></td>
                                                <td><strong><?= "R$ " . number_format($totalGrouppedFinancingCashFlow, 2, ",", ".") ?></strong></td>
                                            </tr>
                                        <?php endif ?>
                                        <tr>
                                            <td><strong>(K) Variação líquida de caixa</strong></td>
                                            <td><strong><?= "R$ " . number_format(array_sum($cashNetVolatility), 2, ",", ".") ?></strong></td>
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