<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatório de categoria de contas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Início</a></li>
                        <li class="breadcrumb-item active">Relatório de categoria de contas</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Relatório de categoria de contas</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                            <table id="chartOfAccountGroup" class="table table-bordered table-striped">
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
                                        <?php if (!empty($chartOfAccountGroupData)) : ?>
                                            <?php foreach ($chartOfAccountGroupData as $value) : ?>
                                                <tr>
                                                    <td><?= $value->getUuid() ?></td>
                                                    <td><?= $value->account_number ?></td>
                                                    <td><?= $value->account_name ?></td>
                                                    <td><a class="icons" href="<?= url("/admin/balance-sheet/chart-of-account-group/update/" . $value->getUuid() . "") ?>"><i class="fas fa-edit" aria-hidden="true"></i></a></td>
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