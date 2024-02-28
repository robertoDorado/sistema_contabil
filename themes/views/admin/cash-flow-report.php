<?php $v->layout("admin/layouts/_admin") ?>
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
                                            <?php foreach($cashFlowDataByUser as $cashFlowData): ?>
                                                <?php if (!empty($cashFlowData->entry_type)) : ?>
                                                    <tr style="color:#008000">
                                                        <td>#<?= $cashFlowData->id ?></td>
                                                        <td><?= $cashFlowData->created_at ?></td>
                                                        <td><?= $cashFlowData->getHistory() ?></td>
                                                        <td><?= $cashFlowData->entry_type_value ?></td>
                                                        <td><?= $cashFlowData->getEntry() ?></td>
                                                        <td><a class="icons" href="#"><i class="fas fa-edit" aria-hidden="true"></i></a></td>
                                                        <td><a class="icons" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a></td>
                                                    </tr>
                                                <?php else : ?>
                                                    <tr style="color:#ff0000">
                                                        <td>#<?= $cashFlowData->id ?></td>
                                                        <td><?= $cashFlowData->created_at ?></td>
                                                        <td><?= $cashFlowData->getHistory() ?></td>
                                                        <td><?= $cashFlowData->entry_type_value ?></td>
                                                        <td><?= $cashFlowData->getEntry() ?></td>
                                                        <td><a class="icons" href="#"><i class="fas fa-edit" aria-hidden="true"></i></a></td>
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
                                        <?php elseif(empty($balanceValue)) : ?>
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