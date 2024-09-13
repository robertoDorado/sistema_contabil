<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatório projeção de fluxo de caixa</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Home</a></li>
                        <li class="breadcrumb-item active">Relatório projeção de fluxo de caixa</li>
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
            <?php if (!empty($incomeData)) : ?>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Entradas de caixa</h3>
                            </div>

                            <div class="card-body">
                                <div id="widgets" class="dataTables_wrapper dt-bootstrap4 cash-flow-projections-income">
                                    <table id="cashFlowProjectionsIncome" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Data ref.</th>
                                                <th>Mês</th>
                                                <th>Nome do grupo de contas</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($incomeData as $data) : ?>
                                                <tr>
                                                    <td><?= $data["date"] ?></td>
                                                    <td><?= $data["month"] ?></td>
                                                    <td><?= $data["group_name"] ?></td>
                                                    <td><?= $data["total_entry"] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if (!empty($expensesData)) : ?>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Saídas de caixa</h3>
                            </div>

                            <div class="card-body">
                                <div id="widgets" class="dataTables_wrapper dt-bootstrap4 cash-flow-projections-expenses">
                                    <table id="cashFlowProjectionsExpenses" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Data ref.</th>
                                                <th>Mês</th>
                                                <th>Nome do grupo de contas</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($expensesData as $data) : ?>
                                                <tr>
                                                    <td><?= $data["date"] ?></td>
                                                    <td><?= $data["month"] ?></td>
                                                    <td><?= $data["group_name"] ?></td>
                                                    <td><?= $data["total_entry"] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if (!empty($projectedCashFlow)) : ?>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Fluxo de caixa projetado</h3>
                            </div>
    
                            <div class="card-body">
                                <div id="widgets" class="dataTables_wrapper dt-bootstrap4 cash-flow-projections">
                                    <table id="cashFlowProjections" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Data ref.</th>
                                                <th>Mês</th>
                                                <th>Total de entradas</th>
                                                <th>Total de saídas</th>
                                                <th>Saldo do mês</th>
                                                <th>Saldo acumulado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($projectedCashFlow as $cashFlow): ?>
                                                <tr>
                                                    <td><?= $cashFlow["date"] ?></td>
                                                    <td><?= $cashFlow["month"] ?></td>
                                                    <td><?= "R$ " . number_format($cashFlow["total_income_value"], 2, ",", ".") ?></td>
                                                    <td><?= "R$ " . number_format($cashFlow["total_expenses_value"], 2, ",", ".") ?></td>
                                                    <td><?= "R$ " . number_format($cashFlow["month_balance"], 2, ",", ".") ?></td>
                                                    <td><?= "R$ " . number_format($cashFlow["accumulated_balance"], 2, ",", ".") ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>