<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Atualização do grupo de contas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin/cash-flow-group/report") ?>">Relatório grupo de contas</a></li>
                        <li class="breadcrumb-item active">Atualização do grupo de contas</li>
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
                            <h3 class="card-title">Atualizar grupo de contas</h3>
                        </div>
                        <form id="cashFlowGroupForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="accountGroup">Nome do grupo</label>
                                    <input name="accountGroup" value="<?= $cashFlowGroupData->group_name ?>" id="accountGroup"class="form-control" placeholder="Exemplo: Despesas fixas">
                                    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= session()->csrf_token ?>">
                                </div>
                            </div>
    
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