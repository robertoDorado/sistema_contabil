<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Formulário balanço patrimonial</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Página Inicial</a></li>
                        <li class="breadcrumb-item active">Formulário balanço patrimonial</li>
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
                        <form id="balanceSheetForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="accountType">Tipo de lançamento</label>
                                    <select name="accountType" id="accountType" class="form-control">
                                        <option value="" disabled selected>Selecione o tipo de lançamento</option>
                                        <option value="1">Crédito</option>
                                        <option value="0">Débito</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="accountHistory">Histórico de lançamento</label>
                                    <textarea class="form-control" name="accountHistory" id="accountHistory"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="accountValue">Valor do lançamento</label>
                                    <input name="accountValue" id="accountValue" class="form-control" placeholder="Exemplo: R$ 12.000,00">
                                    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= session()->csrf_token ?>">
                                </div>
                                <div class="form-group">
                                    <label for="createdAt">Data de lançamento</label>
                                    <input type="text" name="createdAt" class="form-control" id="createdAt" placeholder="Exemplo: 29/10/2000">
                                </div>
                                <div class="form-group">
                                    <label for="chartOfAccountSelect">Plano de contas</label>
                                    <select name="chartOfAccountSelect" id="chartOfAccountSelect" class="form-control">
                                        <option value="" disabled selected>Selecione um plano de contas</option>
                                        <?php if (!empty($chartOfAccountData)) : ?>
                                            <?php foreach ($chartOfAccountData as $value) : ?>
                                                <option value="<?= $value->getUuid() ?>"><?= $value->account_number . " " . $value->account_name ?></option>
                                            <?php endforeach ?>
                                        <?php endif ?>
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