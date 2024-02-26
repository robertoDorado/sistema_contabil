<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Formulário de lançamentos</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Página Inicial</a></li>
                        <li class="breadcrumb-item active">Formulário de lançamentos</li>
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
                            <h3 class="card-title">Fazer um lançamento</h3>
                        </div>
                        <form id="cashFlowForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="launchValue">Valor de Lançamento</label>
                                    <input type="text" name="launchValue" class="form-control" id="launchValue" placeholder="Valor de lançamento">
                                    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= session()->csrf_token ?>">
                                </div>
                                <div class="form-group">
                                    <label for="releaseHistory">Histórico de lançamento</label>
                                    <textarea name="releaseHistory" id="releaseHistory" cols="30" rows="5" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="entryType">Tipo de entrada</label>
                                    <select name="entryType" id="entryType" class="form-control">
                                        <option value="" disabled selected>Selecione o tipo de entrada</option>
                                        <option value="1">Crédito</option>
                                        <option value="0">Débito</option>
                                    </select>
                                </div>
                            </div>
    
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>