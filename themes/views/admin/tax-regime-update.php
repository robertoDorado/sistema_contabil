<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Formulário de atualização do regime tributário</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin/tax-regime/form") ?>">Regime tributário</a></li>
                        <li class="breadcrumb-item active">Formulário regime tributário</li>
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
                            <h3 class="card-title">Atualizar regime tributário</h3>
                        </div>
                        <form id="taxRegimeForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="taxRegimeValue">Regime tributário</label>
                                    <input type="text" data-name="regime tributário" class="form-control" name="taxRegimeValue" id="taxRegimeValue" value="<?= $establishedTaxRegime->tax_regime_value ?>">
                                    <input type="hidden" data-name="token" name="csrfToken" id="csrfToken" value="<?= session()->csrf_token ?>">
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