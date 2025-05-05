<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Responder ticket</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin/support/dashboard") ?>">Home</a></li>
                        <li class="breadcrumb-item active">Responder ticket</li>
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
                            <h3 class="card-title">Formulário de resposta</h3>
                        </div>
                        <form id="supportTicketsFormReply">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Conteúdo do chamado</label>
                                    <div id="ticketData"><?= !empty($supportTicketsData->content_message) ? $supportTicketsData->content_message : "" ?></div>
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
                                    <div id="file" class="file-container">
                                        <img class="ticket-image" src="<?= url("/tickets/{$supportTicketsData->content_attachment}") ?>" alt="<?= $supportTicketsData->content_attachment ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="contentMessage">Conteúdo da mensagem</label>
                                    <textarea name="contentMessage" data-name="Conteúdo da mensagem" class="form-control" id="contentMessage"></textarea>
                                    <input type="hidden" data-name="Token" name="csrfToken" id="csrfToken" value="<?= session()->csrf_token ?>">
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
                            <div class="card-footer">
                                <button type="submit" id="launchBtn" class="btn btn-primary">Responder</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>