<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Página não encontrada</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Página inicial</a></li>
                        <li class="breadcrumb-item active">Página não encontrada</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="content">
        <div class="vh-100 d-flex justify-content-center align-items-center flex-direction-column flex-column">
            <h1 class="text-center"><?= $code ?></h1>
            <h2><?= $errorMessage ?></h2>
        </div>
    </div>
</div>