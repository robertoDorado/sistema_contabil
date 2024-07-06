<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatório backup de notas explicativas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Home</a></li>
                        <li class="breadcrumb-item active">Relatório backup de notas explicativas</li>
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
                            <h3 class="card-title">Relatório de notas</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="cashFlowExplanatoryNotesBackup" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Histórico</th>
                                            <th>Tipo de entrada</th>
                                            <th>Valor</th>
                                            <th>Nota explicativa</th>
                                            <th>Editar</th>
                                            <th>Excluir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($cashFlowExplanatoryNotesData)) : ?>
                                            <?php foreach ($cashFlowExplanatoryNotesData as $value) : ?>
                                                <tr>
                                                    <td><?= $value->history ?></td>
                                                    <td><?= $value->entry_type ?></td>
                                                    <td><?= "R$ " . number_format($value->entry, 2, ",", ".") ?></td>
                                                    <td><?= $value->getNote() ?></td>
                                                    <td><a database-icon data-uuid="<?= $value->getUuid() ?>" data-accountname="<?= $value->history ?>" class="icons restore-icon" href="#"><i class="fas fa-database" aria-hidden="true"></i></a></td>
                                                    <td><a trash-icon data-uuid="<?= $value->getUuid() ?>" data-accountname="<?= $value->history ?>" class="icons delete-icon" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a></td>
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