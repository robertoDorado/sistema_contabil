<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatório fluxo de caixa</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin/cash-flow/form") ?>">Formulário fluxo de caixa</a></li>
                        <li class="breadcrumb-item active">Relatório fluxo de caixa</li>
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
                    <form method="get" action="<?= url("/admin/cash-flow/report") ?>">
                        <div class="form-row">
                            <div class="col-md-6">
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
                <div class="col-md-6 mb-5">
                    <form id="importExcelForm">
                        <div class="mb-3">
                            <label for="fileInput" class="form-label">Selecione um arquivo Excel:</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="excelFile" class="custom-file-input" id="fileInput" accept=".xls,.xlsx">
                                    <label for="excelFile" class="custom-file-label">Nome do arquivo</label>
                                </div>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-cloud-upload"></i> Importar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Relatório de fluxo de caixa</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="cashFlowReport" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Data lançamento</th>
                                            <th>Histórico</th>
                                            <th>Tipo de entrada</th>
                                            <th>Lançamento</th>
                                            <th>Editar</th>
                                            <th>Excluir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (is_array($cashFlowDataByUser) && !empty($cashFlowDataByUser)) : ?>
                                            <?php foreach ($cashFlowDataByUser as $cashFlowData) : ?>
                                                <?php if (!empty($cashFlowData->entry_type)) : ?>
                                                    <tr style="color:#008000">
                                                        <td>#<?= $cashFlowData->uuid_value ?></td>
                                                        <td><?= $cashFlowData->created_at ?></td>
                                                        <td><?= $cashFlowData->getHistory() ?></td>
                                                        <td><?= $cashFlowData->entry_type_value ?></td>
                                                        <td><?= $cashFlowData->getEntry() ?></td>
                                                        <td><a class="icons" href="<?= url("/admin/cash-flow/update/form/" . $cashFlowData->getUuid() . "") ?>"><i class="fas fa-edit" aria-hidden="true"></i></a></td>
                                                        <td><a class="icons" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a></td>
                                                    </tr>
                                                <?php else : ?>
                                                    <tr style="color:#ff0000">
                                                        <td>#<?= $cashFlowData->uuid_value ?></td>
                                                        <td><?= $cashFlowData->created_at ?></td>
                                                        <td><?= $cashFlowData->getHistory() ?></td>
                                                        <td><?= $cashFlowData->entry_type_value ?></td>
                                                        <td><?= $cashFlowData->getEntry() ?></td>
                                                        <td><a class="icons" href="<?= url("/admin/cash-flow/update/form/" . $cashFlowData->getUuid() . "") ?>"><i class="fas fa-edit" aria-hidden="true"></i></a></td>
                                                        <td><a class="icons" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a></td>
                                                    </tr>
                                                <?php endif ?>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </tbody>
                                    <tfoot>
                                        <?php if ($balanceValue > 0) : ?>
                                            <tr style="color:#008000">
                                                <th rowspan="1" colspan="1">Total</th>
                                                <th rowspan="1" colspan="1"></th>
                                                <th rowspan="1" colspan="1"></th>
                                                <th rowspan="1" colspan="1"></th>
                                                <th rowspan="1" colspan="1"><?= $balance ?></th>
                                                <th rowspan="1" colspan="1"></th>
                                                <th rowspan="1" colspan="1"></th>
                                            </tr>
                                        <?php elseif (empty($balanceValue)) : ?>
                                            <tr>
                                                <th rowspan="1" colspan="1">Total</th>
                                                <th rowspan="1" colspan="1"></th>
                                                <th rowspan="1" colspan="1"></th>
                                                <th rowspan="1" colspan="1"></th>
                                                <th rowspan="1" colspan="1">0,00</th>
                                                <th rowspan="1" colspan="1"></th>
                                                <th rowspan="1" colspan="1"></th>
                                            </tr>
                                        <?php else : ?>
                                            <tr style="color:#ff0000">
                                                <th rowspan="1" colspan="1">Total</th>
                                                <th rowspan="1" colspan="1"></th>
                                                <th rowspan="1" colspan="1"></th>
                                                <th rowspan="1" colspan="1"></th>
                                                <th rowspan="1" colspan="1"><?= $balance ?></th>
                                                <th rowspan="1" colspan="1"></th>
                                                <th rowspan="1" colspan="1"></th>
                                            </tr>
                                        <?php endif ?>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <?php if (!empty($cashFlowEmptyMessage)) : ?>
                            <div id="jsonMessage" data-message='<?= $cashFlowEmptyMessage ?>' style="display:none"></div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>