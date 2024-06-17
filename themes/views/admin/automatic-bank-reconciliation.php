<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Conciliação automática do fluxo de caixa</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Home</a></li>
                        <li class="breadcrumb-item active">Conciliação automática do fluxo de caixa</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <div class="content">
        <div class="col-md-6 mb-5">
            <form id="importOfxFile">
                <div class="mb-3">
                    <label for="fileInput" class="form-label">Selecione um arquivo OFX:</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" name="ofxFile" class="custom-file-input" id="fileInput" accept=".ofx">
                            <label for="ofxFile" class="custom-file-label">Nome do arquivo</label>
                        </div>
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-cloud-upload"></i> Importar
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Relatório de fluxo de caixa</h3>
                </div>

                <div class="card-body">
                    <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                        <table id="automaticReconciliationReport" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Histórico</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th rowspan="1" colspan="1">Total</th>
                                    <th rowspan="1" colspan="1"></th>
                                    <th rowspan="1" colspan="1">0,00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>