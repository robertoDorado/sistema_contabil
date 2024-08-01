<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatório balanço patrimonial</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/") ?>">Início</a></li>
                        <li class="breadcrumb-item active">Relatório balanço patrimonial</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="content">
        <div class="container-fluid">
            <div class="row" style="align-items: end">
                <div class="col-md-6 mb-5">
                    <?= $v->insert("admin/layouts/_daterange_input") ?>
                </div>
                <div class="col-md-6 mb-5">
                    <button id="closeAccountingPeriod" class="btn btn-primary">Encerrar período contábil</button>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ativo - Circulante</h3>
                        </div>

                        <div class="card-body">
                            <div id="currentAssetsWidgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="currentAssets" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Data lançamento</th>
                                            <th>Conta</th>
                                            <th>Valor de lançamento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($currentAssetsData)) : ?>
                                            <?php foreach ($currentAssetsData as $currentAssets) : ?>
                                                <tr>
                                                    <td><?= $currentAssets["uuid"] ?></td>
                                                    <td><?= $currentAssets["created_at"] ?></td>
                                                    <td><?= $currentAssets["account_name"] ?></td>
                                                    <td><?= $currentAssets["account_value_format"] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td>Total</td>
                                            <td></td>
                                            <td><?= $totalCurrentAssets ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ativo - Não circulante</h3>
                        </div>

                        <div class="card-body">
                            <div id="nonCurrentAssetsWidgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="nonCurrentAssets" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Data lançamento</th>
                                            <th>Conta</th>
                                            <th>Valor de lançamento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($nonCurrentAssetsData)) : ?>
                                            <?php foreach ($nonCurrentAssetsData as $nonCurrentAssets) : ?>
                                                <tr>
                                                    <td><?= $nonCurrentAssets["uuid"] ?></td>
                                                    <td><?= $nonCurrentAssets["created_at"] ?></td>
                                                    <td><?= $nonCurrentAssets["account_name"] ?></td>
                                                    <td><?= $nonCurrentAssets["account_value_format"] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td>Total</td>
                                            <td></td>
                                            <td><?= $totalNonCurrentAssets ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Passivo - Circulante</h3>
                        </div>

                        <div class="card-body">
                            <div id="currentLiabilitiesWidgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="currentLiabilities" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Data lançamento</th>
                                            <th>Conta</th>
                                            <th>Valor de lançamento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($currentLiabilitiesData)) : ?>
                                            <?php foreach ($currentLiabilitiesData as $currentLiabilities) : ?>
                                                <tr>
                                                    <td><?= $currentLiabilities["uuid"] ?></td>
                                                    <td><?= $currentLiabilities["created_at"] ?></td>
                                                    <td><?= $currentLiabilities["account_name"] ?></td>
                                                    <td><?= $currentLiabilities["account_value_format"] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td>Total</td>
                                            <td></td>
                                            <td><?= $totalCurrentLiabilities ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Passivo - Não circulante</h3>
                        </div>

                        <div class="card-body">
                            <div id="nonCurrentLiabilitiesWidgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="nonCurrentLiabilities" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Data lançamento</th>
                                            <th>Conta</th>
                                            <th>Valor de lançamento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($nonCurrentLiabilitiesData)) : ?>
                                            <?php foreach ($nonCurrentLiabilitiesData as $nonCurrentLiabilities) : ?>
                                                <tr>
                                                    <td><?= $nonCurrentLiabilities["uuid"] ?></td>
                                                    <td><?= $nonCurrentLiabilities["created_at"] ?></td>
                                                    <td><?= $nonCurrentLiabilities["account_name"] ?></td>
                                                    <td><?= $nonCurrentLiabilities["account_value_format"] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td>Total</td>
                                            <td></td>
                                            <td><?= $totalNonCurrentLiabilities ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Patrimônio Líquido</h3>
                        </div>

                        <div class="card-body">
                            <div id="shareholdersEquityWidgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="shareholdersEquity" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Data lançamento</th>
                                            <th>Conta</th>
                                            <th>Valor de lançamento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($shareholdersEquityData)) : ?>
                                            <?php foreach ($shareholdersEquityData as $shareholdersEquity) : ?>
                                                <tr>
                                                    <td><?= $shareholdersEquity["uuid"] ?></td>
                                                    <td><?= $shareholdersEquity["created_at"] ?></td>
                                                    <td><?= $shareholdersEquity["account_name"] ?></td>
                                                    <td data-shareholdersvalue="<?= $shareholdersEquity["account_value"] ?>"><?= $shareholdersEquity["account_value_format"] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td>Total</td>
                                            <td></td>
                                            <td id="totalShareholdersEquity"><?= $totalShareholdersEquity ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Apuração contábil</h3>
                        </div>

                        <div class="card-body">
                            <div id="accountingCalculationWidget" class="dataTables_wrapper dt-bootstrap4">
                                <table id="accountingCalculation" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Total Ativo</th>
                                            <th>Total Passivo + Patrimônio líquido</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?= "R$ " . number_format($accounttingCaculationAssets, 2, ",", ".") ?></td>
                                            <td id="totalShareholdersEquityAndLiabilities"><?= "R$ " . number_format($accountingCalculationLiabilities, 2, ",", ".") ?></td>
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