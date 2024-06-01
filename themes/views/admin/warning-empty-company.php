<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Aviso</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Página inicial</a></li>
                        <li class="breadcrumb-item active">Mensagem de aviso</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="content">
        <div class="container-fluid">
            <div class="content-message">
                <h2>Este é o seu primeiro acesso? Você precisa criar pelo menos uma empresa para continuar usando o sistema.</h2>
                <a href="<?= url("/admin/company/register") ?>">Criar uma nova empresa</a>
            </div>
        </div>
    </div>
</div>