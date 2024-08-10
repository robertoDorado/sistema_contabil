<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Demonstração de resultados do exercício</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Início</a></li>
                        <li class="breadcrumb-item active">Demonstração de resultados do exercício</li>
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
                            <h3 class="card-title">Demonstração de resultados do exercício</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="incomeStatement" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Conta</th>
                                            <th>Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Receita Bruta de vendas</strong></td>
                                            <td><strong><?= $totalRevenue ?></strong></td>
                                        </tr>
                                        <?php if (!empty($totalRevenueSalesData)) : ?>
                                            <?php foreach($totalRevenueSalesData as $revenue): ?>
                                                <tr>
                                                    <td><?= $revenue->account_name ?></td>
                                                    <td><?= $revenue->total_formated ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <tr>
                                            <td><strong>(-) Deduções de vendas</strong></td>
                                            <td><strong><?= $salesDeductions ?></strong></td>
                                        </tr>
                                        <?php if (!empty($salesDeductionsData)) : ?>
                                            <?php foreach($salesDeductionsData as $salesDeduction): ?>
                                                <tr>
                                                    <td><?= $salesDeduction->account_name ?></td>
                                                    <td><?= $salesDeduction->total_formated ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <tr>
                                            <td><strong>(=) Receita Líquida de Vendas</strong></td>
                                            <td><strong><?= $resultRevenueSales ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>(-) Custo das Mercadorias Vendidas (CMV)</strong></td>
                                            <td><strong><?= $costOfSold ?></strong></td>
                                        </tr>
                                        <?php if (!empty($costOfSoldData)) : ?>
                                            <?php foreach($costOfSoldData as $cost): ?>
                                                <tr>
                                                    <td><?= $cost->account_name ?></td>
                                                    <td><?= $cost->total_formated ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <tr>
                                            <td><strong>(=) Lucro Bruto</strong></td>
                                            <td><strong><?= $grossProfit ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>(-) Despesas Operacionais</strong></td>
                                            <td>-</td>
                                        </tr>
                                        <?php if (!empty($operationalExpenses)) : ?>
                                            <?php foreach ($operationalExpenses as $value): ?>
                                                <tr>
                                                    <td><?= $value->account_name ?></td>
                                                    <td><?= $value->total_formated ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <tr>
                                            <td><strong>(=) Resultado Operacional</strong></td>
                                            <td><strong><?= $totalOperationalExpenses ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>(+) Receitas Financeiras</strong></td>
                                            <td><strong><?= $financingRevenue ?></strong></td>
                                        </tr>
                                        <?php if (!empty($financingRevenueData)) : ?>
                                            <?php foreach($financingRevenueData as $financing): ?>
                                                <tr>
                                                    <td><?= $financing->account_name ?></td>
                                                    <td><?= $financing->total_formated ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <tr>
                                            <td><strong>(-) Despesas Financeiras</strong></td>
                                            <td><strong><?= $financialExpenses ?></strong></td>
                                        </tr>
                                        <?php if (!empty($financialExpensesData)) : ?>
                                            <?php foreach($financialExpensesData as $financing): ?>
                                                <tr>
                                                    <td><?= $financing->account_name ?></td>
                                                    <td><?= $financing->total_formated ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <tr>
                                            <td><strong>(=) Resultado Antes dos Tributos</strong></td>
                                            <td><strong><?= $taxesOnProfit ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>(-) Impostos sobre o Lucro</strong></td>
                                            <td><strong><?= $taxProfit ?></strong></td>
                                        </tr>
                                        <?php if (!empty($taxProfitData)) : ?>
                                            <?php foreach($taxProfitData as $tax): ?>
                                                <tr>
                                                    <td><?= $tax->account_name ?></td>
                                                    <td><?= $tax->total_formated ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <tr>
                                            <td><strong>(=) Resultado Líquido do Exercício</strong></td>
                                            <td><strong><?= $resultOfExercise ?></strong></td>
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