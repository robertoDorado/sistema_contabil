<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatório backup de contas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Home</a></li>
                        <li class="breadcrumb-item active">Relatório backup de contas</li>
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
                            <h3 class="card-title">Relatório de contas</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="cashFlowDeletedReport" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Grupo de contas</th>
                                            <th>Data lançamento</th>
                                            <th>Histórico</th>
                                            <th>Tipo de entrada</th>
                                            <th>Lançamento</th>
                                            <th>Restaurar</th>
                                            <th>Excluir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($cashFlowDataByUser)) : ?>
                                            <?php foreach ($cashFlowDataByUser as $cashFlowData) : ?>
                                                <?php if (!empty($cashFlowData->entry_type)) : ?>
                                                    <tr style="color:#008000">
                                                        <td><?= $cashFlowData->getUuid() ?></td>
                                                        <td><?= $cashFlowData->group_name ?></td>
                                                        <td><?= $cashFlowData->created_at ?></td>
                                                        <td><?= $cashFlowData->getHistory() ?></td>
                                                        <td><?= $cashFlowData->entry_type_value ?></td>
                                                        <td><?= $cashFlowData->getEntry() ?></td>
                                                        <td><a class="icons" href="#"><i class="fas fa-database" aria-hidden="true"></i></a></td>
                                                        <td><a class="icons" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a></td>
                                                    </tr>
                                                <?php else : ?>
                                                    <tr style="color:#ff0000">
                                                        <td><?= $cashFlowData->getUuid() ?></td>
                                                        <td><?= $cashFlowData->group_name ?></td>
                                                        <td><?= $cashFlowData->created_at ?></td>
                                                        <td><?= $cashFlowData->getHistory() ?></td>
                                                        <td><?= $cashFlowData->entry_type_value ?></td>
                                                        <td><?= $cashFlowData->getEntry() ?></td>
                                                        <td><a class="icons" href="#"><i class="fas fa-database" aria-hidden="true"></i></a></td>
                                                        <td><a class="icons" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a></td>
                                                    </tr>
                                                <?php endif ?>
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