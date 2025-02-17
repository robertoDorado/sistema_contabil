<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Cancelamento da assinatura</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin/customer/update-data/form") ?>">Formulário do cliente</a></li>
                        <li class="breadcrumb-item active">Cancelamento da assinatura</li>
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
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title">Descreva o motivo do cancelamento e como podemos melhorar o nosso atendimento</h3>
                        </div>
                        <form id="cancelSubscriptionForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="cancelSubscriptionValue">Motivo do cancelamento</label>
                                    <textarea name="cancelSubscriptionValue" class="form-control" id="cancelSubscriptionValue"></textarea>
                                    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= session()->csrf_token ?>">
                                </div>
                            </div>
    
                            <div class="card-footer">
                                <button type="submit" class="btn btn-danger">Cancelar assinatura</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>