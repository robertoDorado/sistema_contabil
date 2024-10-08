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
        <div class="vh-100 d-flex justify-content-center align-items-center flex-direction-column flex-column">
            <h1 class="text-center">Atenção! Ao cancelar sua assinatura, a sua conta ficará permanentemente desativada.</h1>
            <button cancelSubscription class="btn btn-danger btn-lg mt-2" type="button">Cancelar Assinatura Agora Mesmo!</button>
        </div>
    </div>
</div>