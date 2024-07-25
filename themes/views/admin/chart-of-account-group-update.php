<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Atualização da categoria de contas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Início</a></li>
                        <li class="breadcrumb-item active">Atualização da categoria de contas</li>
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
                            <h3 class="card-title">Atualização grupo plano de contas</h3>
                        </div>
                        <form id="chartOfAccountGroupFormUpdate">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="accountNumber">Número da conta</label>
                                    <input data-mask="accountNumber" value="<?= empty($chartOfAccountGroupData->account_number) ? "" : $chartOfAccountGroupData->account_number ?>" data-mask="accountNumber" name="accountNumber" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="accountName">Nome da conta</label>
                                    <input value="<?= empty($chartOfAccountGroupData->account_name) ? "" : $chartOfAccountGroupData->account_name ?>" name="accountName" class="form-control">
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