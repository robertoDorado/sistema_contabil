<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatório backup grupo de contas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Home</a></li>
                        <li class="breadcrumb-item active">Relatório backup grupo de contas</li>
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
                            <h3 class="card-title">Relatório grupo de contas</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="cashFlowGroupDeletedReport" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Nome do grupo de contas</th>
                                            <th>Restaurar</th>
                                            <th>Excluir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($cashFlowGroupData)) : ?>
                                            <?php foreach($cashFlowGroupData as $value): ?>
                                                <tr>
                                                    <td><?= $value->getUuid() ?></td>
                                                    <td><?= $value->group_name ?></td>
                                                    <td><a data-accountname="<?= $value->group_name ?>" data-uuid="<?= $value->getUuid() ?>" class="icons" href="#"><i class="fas fa-database"></i></a></td>
                                                    <td><a data-accountname="<?= $value->group_name ?>" data-uuid="<?= $value->getUuid() ?>" class="icons" href="#"><i class="fas fa-trash" style="color:#ff0000"></i></a></td>
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