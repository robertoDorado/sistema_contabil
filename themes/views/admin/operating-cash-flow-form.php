<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Formulário de fluxo de caixa operacional</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Página Inicial</a></li>
                        <li class="breadcrumb-item active">Formulário de fluxo de caixa operacional</li>
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
                            <h3 class="card-title">Novo fluxo de caixa operacional</h3>
                        </div>
                        <form id="operatingCashFlowForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="accountGroup">Grupo de conta</label>
                                    <select name="accountGroup" id="accountGroup" class="form-control">
                                        <option value="" disabled selected>Selecione um grupo de contas</option>
                                        <?php if (!empty($cashFlowGroupData)) : ?>
                                            <?php foreach ($cashFlowGroupData as $value) : ?>
                                                <option value="<?= $value->getUuid() ?>"><?= $value->group_name ?></option>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </select>
                                    <input type="hidden" name="csrfToken" value="<?= session()->csrf_token ?>">
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