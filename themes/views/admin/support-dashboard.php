<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin/support/dashboard") ?>">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-3">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $totalTicketsOpen ?></h3>
                            <p>Chamados aberto</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $totalTicketsPending ?></h3>
                            <p>Chamados pendentes</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-3">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $totalTicketsUnderAnalysis ?></h3>
                            <p>Chamado em análise</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $totalTicketsResolved ?></h3>
                            <p>Chamados resolvidos</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tickets</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="supportDashboardTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Cliente</th>
                                            <th>Conteúdo do chamado</th>
                                            <th>Conteúdo do anexo</th>
                                            <th>Status</th>
                                            <th>Responder</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($supportTicketsData)) : ?>
                                            <?php foreach($supportTicketsData as $supportTickets): ?>
                                                <tr>
                                                    <td><?= $supportTickets->getUuid() ?></td>
                                                    <td><?= $supportTickets->user_full_name ?></td>
                                                    <td><?= $supportTickets->content_message ?></td>
                                                    <td style="text-align:center"><?= !empty($supportTickets->content_attachment) ? '<i class="fas fa-paperclip"></i>' : "Não possui anexo" ?></td>
                                                    <td style="text-align:center"><span class="right badge <?= $supportTickets->badge ?>"><?= $supportTickets->status?></span></td>
                                                    <td><a href="<?= url("/admin/support/tickets/reply/{$supportTickets->getUuid()}") ?>" class="btn btn-success">Responder</a></td>
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