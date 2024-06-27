<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Atualização de variação de fluxo de caixa</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Página Inicial</a></li>
                        <li class="breadcrumb-item active">Atualização de variação de fluxo de caixa</li>
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
                            <h3 class="card-title">Atualizar variação de fluxo de caixa</h3>
                        </div>
                        <form id="cashVariationForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="accountGroupVariation">Grupo de variação</label>
                                    <select name="accountGroupVariation" id="accountGroupVariation" class="form-control">
                                        <option <?= !empty($cashVariationData) && $cashVariationData->operating ? "selected" : "" ?> value="1">Fluxo de caixa operacional</option>
                                        <option <?= !empty($cashVariationData) && $cashVariationData->investment ? "selected" : "" ?> value="2">Fluxo de caixa de investimento</option>
                                        <option <?= !empty($cashVariationData) && $cashVariationData->financing ? "selected" : "" ?> value="3">Fluxo de caixa de financiamento</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="accountGroup">Grupo de conta</label>
                                    <select name="accountGroup" id="accountGroup" class="form-control">
                                        <?php if (!empty($cashFlowGroupData)) : ?>
                                            <?php foreach ($cashFlowGroupData as $value) : ?>
                                                <option <?= $cashVariationData->uuid == $value->getUuid() ? "selected" : "" ?> value="<?= $value->getUuid() ?>"><?= $value->group_name ?></option>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </select>
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
        </div>
    </div>
</div>