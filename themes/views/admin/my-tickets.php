<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Meus chamados registrados</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Início</a></li>
                        <li class="breadcrumb-item active">Meus chamados registrados</li>
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
                            <h3 class="card-title">Chamados registrados</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="myTicketsReport" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Usuário suporte</th>
                                            <th>Conteúdo do chamado</th>
                                            <th>Conteúdo do anexo</th>
                                            <th>Status</th>
                                            <th>Data de abertura</th>
                                            <th>Resposta</th>
                                            <th>Ver detalhes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($supportTicketsData)) : ?>
                                            <?php foreach ($supportTicketsData as $supportTickets): ?>
                                                <tr>
                                                    <td><?= $supportTickets->getUuid() ?></td>
                                                    <td><?= $supportTickets->user_full_name ?></td>
                                                    <td><?= $supportTickets->content_message_tickets ?></td>
                                                    <td style="text-align:center"><?= !empty($supportTickets->content_attachment_tickets) ? '<i class="fas fa-paperclip"></i>' : "Não possui anexo" ?></td>
                                                    <td style="text-align:center"><span class="right badge <?= $supportTickets->badge ?>"><?= $supportTickets->status ?></span></td>
                                                    <td><?= $supportTickets->created_at_ticket ?></td>
                                                    <?php if ($supportTickets->reply_message) : ?>
                                                        <td><a href="<?= url("/admin/support/tickets/see-reply/{$supportTickets->uuid_support_response}") ?>" class="btn btn-success">Resposta</a></td>
                                                    <?php else : ?>
                                                        <td></td>
                                                    <?php endif ?>
                                                    <td><a href="<?= url("/admin/support/my-tickets/update/{$supportTickets->uuid_ticket}") ?>" class="btn btn-primary">Ver detalhes</a></td>
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