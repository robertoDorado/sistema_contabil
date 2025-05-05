<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detalhes do ticket</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin/support/my-tickets") ?>">Relatório de tickets</a></li>
                        <li class="breadcrumb-item active">Detalhes do ticket</li>
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
                            <h3 class="card-title">Formulário de atualização do ticket</h3>
                        </div>
                        <form id="supportTicketsFormUpdate">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="contentMessage">Conteúdo da mensagem</label>
                                    <textarea value="<?= !empty($supportTicketsData->content_message) ? $supportTicketsData->content_message : "" ?>" name="contentMessage" data-name="Conteúdo da mensagem" class="form-control" id="contentMessage"><?= !empty($supportTicketsData->content_message) ? $supportTicketsData->content_message : "" ?></textarea>
                                    <input type="hidden" data-name="Token" name="csrfToken" id="csrfToken" value="<?= session()->csrf_token ?>">
                                </div>
                                <div class="form-group">
                                    <label for="userSupportData">Conta suporte</label>
                                    <select name="userSupportData" data-name="Conta suporte" id="userSupportData" class="form-control">
                                        <option value="" disabled selected>Selecione uma conta suporte</option>
                                        <?php if (!empty($userSupportData)) : ?>
                                            <?php foreach ($userSupportData as $support) : ?>
                                                <option <?= $supportTicketsData->id_user == $support->id ? "selected" : "" ?> value="<?= $support->getUuid() ?>"><?= $support->user_full_name ?></option>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="ticketStatus">Status</label>
                                    <select name="ticketStatus" data-name="Status" id="ticketStatus" class="form-control">
                                        <option <?= $supportTicketsData->getStatus() == "aberto" ? "selected" : "" ?> value="aberto">aberto</option>
                                        <option <?= $supportTicketsData->getStatus() == "pendente" ? "selected" : "" ?> value="pendente">pendente</option>
                                        <option <?= $supportTicketsData->getStatus() == "em análise" ? "selected" : "" ?> value="em análise">em análise</option>
                                        <option <?= $supportTicketsData->getStatus() == "resolvido" ? "selected" : "" ?> value="resolvido">resolvido</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="attachmentFile" class="form-label">Selecione um arquivo:</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" name="attachmentFile" data-notrequired="true" class="custom-file-input" id="attachmentFile" accept=".jpg,.png">
                                            <label for="attachmentFile" class="custom-file-label">Nome do arquivo</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($supportTicketsData->content_attachment)) : ?>
                                <div class="mb-3">
                                    <div id="file" class="file-container">
                                        <img class="ticket-image" src="<?= url("/tickets/{$supportTicketsData->content_attachment}") ?>" alt="<?= $supportTicketsData->content_attachment ?>">
                                    </div>
                                </div>
                            <?php endif ?>
                            <div class="card-footer">
                                <button type="submit" id="launchBtn" class="btn btn-primary">Atualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>