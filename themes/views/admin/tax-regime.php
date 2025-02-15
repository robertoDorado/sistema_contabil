<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Regime tributário</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Início</a></li>
                        <li class="breadcrumb-item active">Regime tributário</li>
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
                    <button class="btn btn-primary" tax-regime-btn>Auto preenchimento do regime tributário</button>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Modelo de regime tibutário</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="taxRegimeReport" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Nome da conta tributária</th>
                                            <th>Editar</th>
                                            <th>Excluir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($taxRegimeModelData)) : ?>
                                            <?php foreach ($taxRegimeModelData as $taxRegime) : ?>
                                                <tr>
                                                    <td><?= $taxRegime->getUuid() ?></td>
                                                    <td><?= $taxRegime->tax_regime_value ?></td>
                                                    <td><a class="icons" href="<?= url("/admin/tax-regime/form/update/" . $taxRegime->getUuid() . "") ?>"><i class="fas fa-edit" aria-hidden="true"></i></a></td>
                                                    <td><a data-name="<?= $taxRegime->tax_regime_value ?>" data-uuid="<?= $taxRegime->getUuid() ?>" class="icons trash-icon" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a></td>
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
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Formulário Regime Tributário</h3>
                        </div>
                        <form id="taxRegimeForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="taxRegimeValue">Regime tributário</label>
                                    <input class="form-control" type="text" name="taxRegimeValue" id="taxRegimeValue">
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
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Atual regime tributário da empresa</h3>
                        </div>
                        <form id="setTaxRegimeForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <select id="taxRegimeSelectMultiple" name="taxRegimeSelectMultiple" style="width: 50%">
                                        <?php if (!empty($establishedTaxRegime)) : ?>
                                            <option value="" disabled>Selecione o atual regime tributário da empresa</option>
                                        <?php else : ?>
                                            <option value="" disabled selected>Selecione o atual regime tributário da empresa</option>
                                        <?php endif ?>
                                        <?php if (!empty($taxRegimeModelData)) : ?>
                                            <?php foreach ($taxRegimeModelData as $taxRegimeModel) : ?>
                                                <?php if (!empty($establishedTaxRegime) && $taxRegimeModel->getUuid() === $establishedTaxRegime->uuid_tax_regime_model) : ?>
                                                    <option value="<?= $taxRegimeModel->getUuid() ?>" selected><?= $taxRegimeModel->tax_regime_value ?></option>
                                                <?php else : ?>
                                                    <option value="<?= $taxRegimeModel->getUuid() ?>"><?= $taxRegimeModel->tax_regime_value ?></option>
                                                <?php endif ?>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </select>
                                    <input type="hidden" name="updateTaxRegime" value="<?= empty($establishedTaxRegime) ? "" : $establishedTaxRegime->uuid_tax_regime ?>">
                                    <input type="hidden" name="csrfToken" value="<?= session()->csrf_token ?>">
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" id="setTaxRegimeBtn" class="btn btn-primary">Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>