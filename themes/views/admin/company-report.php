<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatório de empresas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin/company/register") ?>">Formulário de empresas</a></li>
                        <li class="breadcrumb-item active">Relatório de empresas</li>
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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Relatório de empresas</h3>
                        </div>

                        <div class="card-body">
                            <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                <table id="companyReport" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Nome da empresa</th>
                                            <th>CNPJ</th>
                                            <th>Inscrição estadual</th>
                                            <th>Data de abertura</th>
                                            <th>Site</th>
                                            <th>E-mail</th>
                                            <th>CEP</th>
                                            <th>Endereço</th>
                                            <th>Número</th>
                                            <th>Bairro</th>
                                            <th>Cidade</th>
                                            <th>Estado</th>
                                            <th>Telefone</th>
                                            <th>Celular</th>
                                            <th>Editar</th>
                                            <th>Excluir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($companyData)) : ?>
                                            <?php foreach ($companyData as $company) : ?>
                                                <tr>
                                                    <td><?= $company->getUuid() ?></td>
                                                    <td><?= $company->company_name ?></td>
                                                    <td><?= $company->company_document ?></td>
                                                    <td><?= $company->state_registration ?></td>
                                                    <td><?= date("d/m/Y", strtotime($company->opening_date)) ?></td>
                                                    <td><?= $company->web_site ?></td>
                                                    <td><?= $company->company_email ?></td>
                                                    <td><?= $company->company_zipcode ?></td>
                                                    <td><?= $company->company_address ?></td>
                                                    <td><?= $company->company_address_number ?></td>
                                                    <td><?= $company->company_neighborhood ?></td>
                                                    <td><?= $company->company_city ?></td>
                                                    <td><?= $company->company_state ?></td>
                                                    <td><?= $company->company_phone ?></td>
                                                    <td><?= $company->company_cell_phone ?></td>
                                                    <td><a class="icons" href="<?= url("/admin/company/update/form/" . $company->getUuid() . "") ?>"><i class="fas fa-edit" aria-hidden="true"></i></a></td>
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