<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Balancete de verificação</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Início</a></li>
                        <li class="breadcrumb-item active">Balancete de verificação</li>
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
                    <?= $v->insert("admin/layouts/_daterange_input") ?>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Balancete de verificação</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="trialBalanceReport" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Data de lançamento</th>
                                            <th>Conta</th>
                                            <th>Saldo devedor</th>
                                            <th>Saldo credor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($trialBalanceData)) : ?>
                                            <?php foreach ($trialBalanceData as $trialBalance) : ?>
                                                <tr>
                                                    <td><?= $trialBalance->getUuid() ?></td>
                                                    <td><?= $trialBalance->created_at ?></td>
                                                    <td><?= $trialBalance->account_name ?></td>
                                                    <td><?= $trialBalance->outstanding_balance ?></td>
                                                    <td><?= $trialBalance->credit_balance ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th>Total</th>
                                            <th></th>
                                            <th><?= $totalTrialBalance->outstanding_balance ?></th>
                                            <th><?= $totalTrialBalance->credit_balance ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>