<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Resposta do ticket</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin/support/my-tickets") ?>">Relatório de tickets</a></li>
                        <li class="breadcrumb-item active">Resposta do ticket</li>
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
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Resposta</h3>
                        </div>
                        <form id="supportTicketsFormReply">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Data</label>
                                    <div id="createdAt"><?= !empty($supportResponseData->created_at) ? $supportResponseData->created_at : "" ?></div>
                                </div>
                                <div class="form-group">
                                    <label>Usuário suporte</label>
                                    <div id="createdAt"><?= !empty($supportResponseData->user_full_name) ? $supportResponseData->user_full_name : "" ?></div>
                                </div>
                                <div class="form-group">
                                    <label>Conteúdo da resposta</label>
                                    <div id="ticketData"><?= !empty($supportResponseData->content_message) ? $supportResponseData->content_message : "" ?></div>
                                </div>
                                <div class="mb-3">
                                    <div id="file">
                                        <img class="ticket-image" src="<?= url("/tickets/{$supportResponseData->content_attachment}") ?>" alt="<?= $supportResponseData->content_attachment ?>">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>