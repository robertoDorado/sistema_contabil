<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatório fluxo de caixa</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin/cash-flow/form") ?>">Formulário fluxo de caixa</a></li>
                        <li class="breadcrumb-item active">Relatório fluxo de caixa</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 mb-5">
                    <?= $v->insert("admin/layouts/_daterange_input") ?>
                </div>
                <div class="col-md-6 mb-5">
                    <form id="importExcelForm">
                        <div class="mb-3">
                            <label for="fileInput" class="form-label">Selecione um arquivo Excel:</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="excelFile" class="custom-file-input" id="fileInput" accept=".xls,.xlsx,.csv">
                                    <label for="excelFile" class="custom-file-label">Nome do arquivo</label>
                                </div>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-cloud-upload"></i> Importar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <?= $v->insert("admin/layouts/_cashflow_report.php") ?>
            </div>
        </div>
    </div>
</div>