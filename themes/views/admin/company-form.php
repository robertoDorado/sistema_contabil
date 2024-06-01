<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Formulário de empresas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Página Inicial</a></li>
                        <li class="breadcrumb-item active">Formulário de empresas</li>
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
                            <h3 class="card-title">Criar uma nova empresa</h3>
                        </div>
                        <form id="companyForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="companyName">Nome da empresa</label>
                                    <input type="text" class="form-control" name="companyName" id="companyName">
                                    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= session()->csrf_token ?>">
                                </div>
                                <div class="form-group">
                                    <label for="companyDocument">CNPJ</label>
                                    <input type="text" data-mask="cnpj" class="form-control" name="companyDocument" id="companyDocument">
                                </div>
                                <div class="form-group">
                                    <label for="stateRegistration">Inscrição estadual</label>
                                    <input type="text" data-mask="inscricaoEstadual" class="form-control" name="stateRegistration" id="stateRegistration">
                                </div>
                                <div class="form-group date" data-date-format="dd/mm/yyyy">
                                    <label for="openingDate">Data de abertura</label>
                                    <input type="text" data-mask="date" class="form-control" name="openingDate" id="openingDate">
                                </div>
                                <div class="form-group">
                                    <label for="webSite">Site da empresa</label>
                                    <input type="text" class="form-control" name="webSite" id="webSite">
                                </div>
                                <div class="form-group">
                                    <label for="companyEmail">E-mail da empresa</label>
                                    <input type="text" class="form-control" name="companyEmail" id="companyEmail">
                                </div>
                                <div class="form-group">
                                    <label for="companyZipcode">CEP</label>
                                    <input type="text" data-mask="cep" class="form-control" name="companyZipcode" id="companyZipcode">
                                </div>
                                <div class="form-group">
                                    <label for="companyAddress">Endereço</label>
                                    <input type="text" class="form-control" name="companyAddress" id="companyAddress">
                                </div>
                                <div class="form-group">
                                    <label for="companyAddressNumber">Número</label>
                                    <input type="text" data-mask="number" class="form-control" name="companyAddressNumber" id="companyAddressNumber">
                                </div>
                                <div class="form-group">
                                    <label for="companyNeighborhood">Bairro</label>
                                    <input type="text" class="form-control" name="companyNeighborhood" id="companyNeighborhood">
                                </div>
                                <div class="form-group">
                                    <label for="companyCity">Cidade</label>
                                    <input type="text" class="form-control" name="companyCity" id="companyCity">
                                </div>
                                <div class="form-group">
                                    <label for="companyState">Estado</label>
                                    <input type="text" data-mask="state" class="form-control" name="companyState" id="companyState">
                                </div>
                                <div class="form-group">
                                    <label for="companyPhone">Telefone</label>
                                    <input type="text" data-mask="phone" class="form-control" name="companyPhone" id="companyPhone">
                                </div>
                                <div class="form-group">
                                    <label for="companyCellPhone">Celular</label>
                                    <input type="text" data-mask="cellPhone" class="form-control" name="companyCellPhone" id="companyCellPhone">
                                </div>
                            </div>
    
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>