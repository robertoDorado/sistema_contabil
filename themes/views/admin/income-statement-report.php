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
                                            <td><?= $totalRevenue ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>(-) Deduções de vendas</strong></td>
                                            <td><?= $salesDeductions ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>(=) Receita Líquida de Vendas</strong></td>
                                            <td><?= $resultRevenueSales ?></td>
                                        </tr>
                                        <tr>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>(-) Custo das Mercadorias Vendidas (CMV)</strong></td>
                                            <td><?= $costOfSold ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>(=) Lucro Bruto</strong></td>
                                            <td><?= $grossProfit ?></td>
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
                                            <td><?= $totalOperationalExpenses ?></td>
                                        </tr>
                                        <tr>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>(+) Receitas Financeiras</strong></td>
                                            <td><?= $financingRevenue ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>(-) Despesas Financeiras</strong></td>
                                            <td>{variavel}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>(=) Resultado Antes dos Tributos</strong></td>
                                            <td>{variavel}</td>
                                        </tr>
                                        <tr>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>(-) Impostos sobre o Lucro</strong></td>
                                            <td>{variavel}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>(=) Resultado Líquido do Exercício</strong></td>
                                            <td>{variavel}</td>
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