<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatório de notas explicativas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin/cash-flow-group/form") ?>">Formulário de notas explicativas</a></li>
                        <li class="breadcrumb-item active">Relatório de notas explicativas</li>
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
                            <h3 class="card-title">Relatório de notas explicativas</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="cashFlowExplanatoryNotesReport" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Histórico</th>
                                            <th>Valor</th>
                                            <th>Nota explicativa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($cashFlowExplanatoryNotesData)) : ?>
                                            <?php foreach($cashFlowExplanatoryNotesData as $value): ?>
                                                <tr>
                                                    <td><?= $value->getUuid() ?></td>
                                                    <td><?= $value->history ?></td>
                                                    <td><?= "R$ " . number_format($value->entry, 2, ",", ".") ?></td>
                                                    <td><?= $value->note ?></td>
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