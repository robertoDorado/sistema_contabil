<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatório de Auditoria</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin/company/register") ?>">Formulário de Auditoria</a></li>
                        <li class="breadcrumb-item active">Relatório de Auditoria</li>
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
                            <h3 class="card-title">Relatório de Auditoria</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="historyAuditReport" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Relatório de origem</th>
                                            <th>Histórico</th>
                                            <th>Valor</th>
                                            <th>Data do registro</th>
                                            <th>Hora do registro</th>
                                            <th>Editar</th>
                                            <th>Excluir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($historyAuditData)) : ?>
                                            <?php foreach ($historyAuditData as $historyAudit) : ?>
                                                <tr>
                                                    <td><?= $historyAudit->getUuid() ?></td>
                                                    <td><?= $historyAudit->report_name ?></td>
                                                    <td><?= $historyAudit->history_transaction ?></td>
                                                    <td><?= $historyAudit->transaction_value ?></td>
                                                    <td><?= $historyAudit->date_created_at ?></td>
                                                    <td><?= $historyAudit->time_created_at ?></td>
                                                    <td><a class="icons" href="<?= url("/admin/history-audit/form/" . $historyAudit->getUuid() . "") ?>"><i class="fas fa-edit" aria-hidden="true"></i></a></td>
                                                    <td><a data-csrf="<?= session()->csrf_token ?>" data-company="<?= $historyAudit->history_transaction ?>" data-uuid="<?= $historyAudit->getUuid() ?>" class="icons trash-link" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a></td>
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