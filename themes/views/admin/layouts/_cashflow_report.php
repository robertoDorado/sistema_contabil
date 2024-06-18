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
                            <th>Grupo de contas</th>
                            <th>Data lançamento</th>
                            <th>Histórico</th>
                            <th>Tipo de entrada</th>
                            <th>Lançamento</th>
                            <?php if (!empty($hasControls)) : ?>
                                <th>Editar</th>
                                <th>Excluir</th>
                            <?php endif ?>
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
                                        <?php if (!empty($hasControls)) : ?>
                                            <td><a class="icons" href="<?= url("/admin/cash-flow/update/form/" . $cashFlowData->getUuid() . "") ?>"><i class="fas fa-edit" aria-hidden="true"></i></a></td>
                                            <td><a class="icons" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a></td>
                                        <?php endif ?>
                                    </tr>
                                <?php else : ?>
                                    <tr style="color:#ff0000">
                                        <td><?= $cashFlowData->getUuid() ?></td>
                                        <td><?= $cashFlowData->group_name ?></td>
                                        <td><?= $cashFlowData->created_at ?></td>
                                        <td><?= $cashFlowData->getHistory() ?></td>
                                        <td><?= $cashFlowData->entry_type_value ?></td>
                                        <td><?= $cashFlowData->getEntry() ?></td>
                                        <?php if (!empty($hasControls)) : ?>
                                            <td><a class="icons" href="<?= url("/admin/cash-flow/update/form/" . $cashFlowData->getUuid() . "") ?>"><i class="fas fa-edit" aria-hidden="true"></i></a></td>
                                            <td><a class="icons" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a></td>
                                        <?php endif ?>
                                    </tr>
                                <?php endif ?>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                    <tfoot>
                        <?php if (!empty($balanceValue)) : ?>
                            <?php if ($balanceValue > 0) : ?>
                                <tr style="color:#008000">
                                    <th rowspan="1" colspan="1">Total</th>
                                    <th rowspan="1" colspan="1"></th>
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
                                    <th rowspan="1" colspan="1"></th>
                                    <th rowspan="1" colspan="1"><?= $balance ?></th>
                                    <th rowspan="1" colspan="1"></th>
                                    <th rowspan="1" colspan="1"></th>
                                </tr>
                            <?php endif ?>
                        <?php endif ?>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>