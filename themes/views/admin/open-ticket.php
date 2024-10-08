<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Abrir um chamado</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Página Inicial</a></li>
                        <li class="breadcrumb-item active">Abrir um chamado</li>
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
                            <h3 class="card-title">Formulário de abertura de chamado</h3>
                        </div>
                        <form id="supportTicketsForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="contentMessage">Conteúdo da mensagem</label>
                                    <textarea name="contentMessage" data-name="Conteúdo da mensagem" class="form-control" id="contentMessage"></textarea>
                                    <input type="hidden" data-name="Token" name="csrfToken" id="csrfToken" value="<?= session()->csrf_token ?>">
                                </div>
                                <div class="form-group">
                                    <label for="userSupportData">Conta suporte</label>
                                    <select name="userSupportData" data-name="Conta suporte" id="userSupportData" class="form-control">
                                        <option value="" disabled selected>Selecione uma conta suporte</option>
                                        <?php if (!empty($userSupportData)) : ?>
                                            <?php foreach ($userSupportData as $support) : ?>
                                                <option value="<?= $support->getUuid() ?>"><?= $support->user_full_name ?></option>
                                            <?php endforeach ?>
                                        <?php endif ?>
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
                            <div class="card-footer">
                                <button type="submit" id="launchBtn" class="btn btn-primary">Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>