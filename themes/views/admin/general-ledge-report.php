<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Livro razão</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Início</a></li>
                        <li class="breadcrumb-item active">Livro razão</li>
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
                    <form id="gerneralLedgeSearch" method="get" action="#">
                        <div class="form-row">
                            <div class="col-md-6">
                                <label for="selectChartOfAccountMultiple">Selecione as contas</label>
                                <select class="form-control" name="selectChartOfAccountMultiple[]" multiple="multiple" id="selectChartOfAccountMultiple">
                                    <?php foreach ($chartOfAccountData as $chartOfAccount) : ?>
                                        <?php if (in_array($chartOfAccount->getUuid(), $chartOfAccountSelected)) : ?>
                                            <option value="<?= $chartOfAccount->getUuid() ?>" selected><?= $chartOfAccount->account_name ?></option>
                                        <?php else : ?>
                                            <option value="<?= $chartOfAccount->getUuid() ?>"><?= $chartOfAccount->account_name ?></option>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="date-range">Busca por data:</label>
                                <input type="text" value="<?= empty($_GET['daterange']) ? "" : $_GET['daterange'] ?>" name="daterange" id="date-range" class="form-control" />
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block" id="btn-search">Buscar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Livro razão</h3>
                    </div>

                    <div class="card-body">
                        <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                            <table id="generalLedgeReport" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Data de lançamento</th>
                                        <th>Grupo de contas</th>
                                        <th>Conta</th>
                                        <th>Histórico</th>
                                        <th>Débito</th>
                                        <th>Crédito</th>
                                        <th>Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($generalLedgeData)) : ?>
                                        <?php foreach ($generalLedgeData as $generalLedge) : ?>
                                            <tr>
                                                <td><?= $generalLedge->getUuid() ?></td>
                                                <td><?= $generalLedge->created_at ?></td>
                                                <td><?= $generalLedge->account_name_group ?></td>
                                                <td><?= $generalLedge->account_name ?></td>
                                                <td><?= $generalLedge->history_account ?></td>
                                                <td><?= $generalLedge->outstanding_balance ?></td>
                                                <td><?= $generalLedge->credit_balance ?></td>
                                                <td><?= $generalLedge->balance ?></td>
                                            </tr>
                                        <?php endforeach ?>
                                    <?php endif ?>
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