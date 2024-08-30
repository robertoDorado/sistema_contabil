<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Formulário cancelamento de NF-e</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Página Inicial</a></li>
                        <li class="breadcrumb-item active">Formulário cancelamento de NF-e</li>
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
                            <h3 class="card-title">Cancelar NF-e</h3>
                        </div>
                        <form id="invoiceCancelForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="reasonOfCancellation">Motivo de cancelamento</label>
                                    <textarea data-name="Motivo do cancelamento" name="reasonOfCancellation" id="reasonOfCancellation" class="form-control"></textarea>
                                    <input type="hidden" data-name="CsrfToken" name="csrfToken" value="<?= session()->csrf_token ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="fileInput" class="form-label">Selecione um certificado PFX:</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" data-name="Certificado PFX" name="pfxFile" class="custom-file-input" id="fileInput" accept=".pfx">
                                            <label for="pfxFile" class="custom-file-label">Nome do arquivo</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="certPassword">Senha do certificado</label>
                                    <input type="password" data-name="Senha do certificado" class="form-control" name="certPassword" id="certPassword">
                                </div>
                                <div class="form-group">
                                    <label for="environment">Ambiente</label>
                                    <select name="environment" data-name="Ambiente" id="environment" class="form-control">
                                        <option value="" disabled selected>Selecione um ambiente</option>
                                        <option value="1">Produção</option>
                                        <option value="2">Homologação</option>
                                    </select>
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