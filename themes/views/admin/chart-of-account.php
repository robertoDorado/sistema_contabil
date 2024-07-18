<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Formulário plano de contas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Página Inicial</a></li>
                        <li class="breadcrumb-item active">Formulário plano de contas</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center">
                    <button id="exportExcelModelChartOfAccount" type="submit" class="btn btn-primary">Exportar modelo de plano de contas</button>
                </div>
                <div class="col-md-6">
                    <form id="importExcelForm">
                        <div class="mb-3">
                            <label for="fileInput" class="form-label">Selecione um arquivo em Excel ou CSV:</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="excelFile" class="custom-file-input" id="fileInput" accept=".xls,.xlsx,.csv">
                                    <label for="excelFile" class="custom-file-label">Nome do arquivo</label>
                                </div>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-cloud-upload"></i> Importar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Plano de contas</h3>
                        </div>
                        <form id="chartOfAccountForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="accountValue">Conta</label>
                                    <input name="accountValue" data-mask="accountValue" class="form-control">
                                    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= session()->csrf_token ?>">
                                </div>
                                <div class="form-group">
                                    <label for="accountName">Nome</label>
                                    <input name="accountName" class="form-control">
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" id="launchBtn" class="btn btn-primary">Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Relatório plano de contas</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="chartOfAccount" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Conta</th>
                                            <th>Nome</th>
                                            <th>Editar</th>
                                            <th>Excluir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($chartOfAccountData)) : ?>
                                            <?php foreach ($chartOfAccountData as $value) : ?>
                                                <tr>
                                                    <td><?= $value->getUuid() ?></td>
                                                    <td><?= $value->account_number ?></td>
                                                    <td><?= $value->account_name ?></td>
                                                    <td><a class="icons" href="<?= url("/admin/balance-sheet/chart-of-account/update/" . $value->getUuid() . "") ?>"><i class="fas fa-edit" aria-hidden="true"></i></a></td>
                                                    <td><a class="icons trash-link" data-accountname="<?= $value->account_name ?>" data-uuid="<?= $value->getUuid() ?>" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a></td>
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