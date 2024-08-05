<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatório livro diário</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Início</a></li>
                        <li class="breadcrumb-item active">Relatório livro diário</li>
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
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Relatório livro diário</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="dailyJournalReport" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Data criada</th>
                                            <th>Conta</th>
                                            <th>Débito/Crédito</th>
                                            <th>Valor</th>
                                            <th>Histórico</th>
                                            <th>Editar</th>
                                            <th>Excluir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($dailyJournalData)) : ?>
                                            <?php foreach ($dailyJournalData as $dailyJournal) : ?>
                                                <tr>
                                                    <td><?= $dailyJournal["uuid"] ?></td>
                                                    <td><?= $dailyJournal["created_at"] ?></td>
                                                    <td><?= $dailyJournal["account_name"] ?></td>
                                                    <td><?= $dailyJournal["account_type"] ?></td>
                                                    <td><?= $dailyJournal["account_value"] ?></td>
                                                    <td><?= $dailyJournal["history_account"] ?></td>
                                                    <td><a class="icons" href="<?= url("/admin/balance-sheet/daily-journal/form/" . $dailyJournal["uuid"] . "") ?>"><i class="fas fa-edit" aria-hidden="true"></i></a></td>
                                                    <td><a class="icons" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a></td>
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