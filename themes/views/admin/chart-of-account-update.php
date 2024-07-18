<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Atualização do plano de contas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin/balance-sheet/chart-of-account") ?>">Plano de contas</a></li>
                        <li class="breadcrumb-item active">Atualização do plano de contas</li>
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
                            <h3 class="card-title">Plano de contas</h3>
                        </div>
                        <form id="chartOfAccountForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="accountValue">Conta</label>
                                    <input name="accountValue" value="<?= empty($chartOfAccountData->account_number) ? "" : $chartOfAccountData->account_number ?>" data-mask="accountValue" class="form-control">
                                    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= session()->csrf_token ?>">
                                </div>
                                <div class="form-group">
                                    <label for="accountName">Nome</label>
                                    <input name="accountName" value="<?= empty($chartOfAccountData->account_name) ? "" : $chartOfAccountData->account_name ?>" class="form-control">
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