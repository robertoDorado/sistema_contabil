<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Demonstração de valor adicionado (DVA)</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Início</a></li>
                        <li class="breadcrumb-item active">Demonstração de valor adicionado (DVA)</li>
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
                            <h3 class="card-title">Demonstração de valor adicionado</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="statementOfValueAdded" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Conta</th>
                                            <th>Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>1. Receitas</strong></td>
                                            <td>-</td>
                                        </tr>
                                        <?php if (!empty($statementOfValueAdded["receitas de vendas de produtos e servicos"])) : ?>
                                            <?php foreach ($statementOfValueAdded["receitas de vendas de produtos e servicos"] as $value): ?>
                                                <tr>
                                                    <td><?= $value->account_name ?></td>
                                                    <td><?= $value->total_formated ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <?php if (!empty($statementOfValueAdded["imposto de renda e contribuicao social"])) : ?>
                                            <?php foreach ($statementOfValueAdded["imposto de renda e contribuicao social"] as $value): ?>
                                                <tr>
                                                    <td><?= $value->account_name ?></td>
                                                    <td><?= $value->total_formated ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <tr>
                                            <td><strong>Receita líquida</strong></td>
                                            <td><strong><?= $netIncome ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>2. Insumos Adquiridos de Terceiros</strong></td>
                                            <td>-</td>
                                        </tr>
                                        <?php if (!empty($statementOfValueAdded["despesas operacionais"])) : ?>
                                            <?php foreach ($statementOfValueAdded["despesas operacionais"] as $value): ?>
                                                <tr>
                                                    <td><?= $value->account_name ?></td>
                                                    <td><?= $value->total_formated ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <?php if (!empty($statementOfValueAdded["custo das vendas"])) : ?>
                                            <?php foreach ($statementOfValueAdded["custo das vendas"] as $value): ?>
                                                <tr>
                                                    <td><?= $value->account_name ?></td>
                                                    <td><?= $value->total_formated ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <tr>
                                            <td><strong>Valor adicionado bruto</strong></td>
                                            <td><strong><?= $grossAddValue ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>3. Retificações</strong></td>
                                            <td>-</td>
                                        </tr>
                                        <?php if (!empty($statementOfValueAdded["retificacoes"])) : ?>
                                            <?php foreach ($statementOfValueAdded["retificacoes"] as $value): ?>
                                                <tr>
                                                    <td><?= $value->account_name ?></td>
                                                    <td><?= $value->total_formated ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <tr>
                                            <td><strong>Valor adicionado líquido</strong></td>
                                            <td><strong><?= $netAddedValue ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>4. Valor Adicionado Recebido em Transferência</strong></td>
                                            <td>-</td>
                                        </tr>
                                        <?php if (!empty($statementOfValueAdded["receitas operacionais"])) : ?>
                                            <?php foreach ($statementOfValueAdded["receitas operacionais"] as $value): ?>
                                                <tr>
                                                    <td><?= $value->account_name ?></td>
                                                    <td><?= $value->total_formated ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <tr>
                                            <td><strong>Valor Adicionado Total a Distribuir</strong></td>
                                            <td><strong><?= $totalAddValueDistributed ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>5. Distribuição do Valor Adicionado</strong></td>
                                            <td><strong>-</strong></td>
                                        </tr>
                                        <?php if (!empty($statementOfValueAdded["Pessoal (Salários, Benefícios, Encargos)"])) : ?>
                                            <?php foreach ($statementOfValueAdded["Pessoal (Salários, Benefícios, Encargos)"] as $value): ?>
                                                <tr>
                                                    <td>Pessoal (Salários, Benefícios, Encargos)</td>
                                                    <td><?= $value->total_formated ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <?php if (!empty($statementOfValueAdded["dva"])) : ?>
                                            <?php foreach ($statementOfValueAdded["dva"] as $key => $data): ?>
                                                <tr>
                                                    <td><?= $key ?></td>
                                                    <td><?= $data->total_formated ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <tr>
                                            <td>Retenção de Lucros (Reservas)</td>
                                            <td><?= $profitRetention ?></td>
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