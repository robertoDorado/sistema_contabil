<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatório variação de fluxo de caixa</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin/cash-variation-setting/form") ?>">Formulário variação de fluxo de caixa</a></li>
                        <li class="breadcrumb-item active">Relatório variação de fluxo de caixa</li>
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
                    <form action="#" id="searchCashFlowVariation">
                        <div class="form-group">
                            <label for="accountGroupVariation">Grupo de variação</label>
                            <select name="accountGroupVariation" id="accountGroupVariation" class="form-control">
                                <option value="1" <?= empty(session()->user->account_group_variation_id) ? "selected" : (!empty(session()->user->account_group_variation_id) && session()->user->account_group_variation_id == 1 ? "selected" : "") ?> >Fluxo de caixa operacional</option>
                                <option value="2" <?= !empty(session()->user->account_group_variation_id) && session()->user->account_group_variation_id == 2 ? "selected" : "" ?>>Fluxo de caixa de investimento</option>
                                <option value="3" <?= !empty(session()->user->account_group_variation_id) && session()->user->account_group_variation_id == 3 ? "selected" : "" ?>>Fluxo de caixa de financiamento</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Variação de fluxo de caixa</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="cashFlowVariation" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Grupo de conta</th>
                                            <th>Editar</th>
                                            <th>Excluir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($responseData)) : ?>
                                            <?php foreach ($responseData as $response) : ?>
                                                <tr>
                                                    <td><?= $response->uuid ?></td>
                                                    <td><?= $response->group_name ?></td>
                                                    <td><a class="icons" href="<?= url("/admin/cash-variation-setting/form-update/" . $response->uuid) ?>"><i class="fas fa-edit" aria-hidden="true"></i></a></td>
                                                    <td><a class="icons" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a></td>
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