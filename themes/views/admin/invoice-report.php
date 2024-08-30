<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatório de notas fiscais</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Início</a></li>
                        <li class="breadcrumb-item active">Relatório de notas fiscais</li>
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
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Notas fiscais</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="invoiceReport" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Nome da empresa</th>
                                            <th>Número de protocolo</th>
                                            <th>Chave de acesso</th>
                                            <th>Emissão da Danfe</th>
                                            <th>Cancelar NF-e</th>
                                            <th>Data criada</th>
                                            <th>Excluir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($invoiceReportData)) : ?>
                                            <?php foreach ($invoiceReportData as $invoice) : ?>
                                                <tr>
                                                    <td><?= $invoice->getUuid() ?></td>
                                                    <td><?= $invoice->company_name ?></td>
                                                    <td><?= $invoice->protocol_number ?></td>
                                                    <td><?= $invoice->access_key ?></td>
                                                    <td><a id="danfeEmission" data-uuid="<?= $invoice->getUuid() ?>" href="#" class="btn btn-primary">Emissão da Danfe</a></td>
                                                    <td><a href="<?= url("/admin/invoice/cancel/nfe/" . $invoice->getUuid() . "") ?>" class="btn btn-danger">Cancelar NF-e</a></td>
                                                    <td><?= $invoice->created_at ?></td>
                                                    <td><a data-uuid="<?= $invoice->getUuid() ?>" data-id="<?= "NFe" . $invoice->access_key ?>" id="deleteInvoice" class="icons" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>