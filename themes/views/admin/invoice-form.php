<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Emissão de nota fiscal</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Página Inicial</a></li>
                        <li class="breadcrumb-item active">Emissão de nota fiscal</li>
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
                            <h3 class="card-title">Emitir uma nova nota</h3>
                        </div>
                        <form id="invoiceForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="companyName">Razão Social</label>
                                    <input type="text" data-name="Razão Social" value="<?= !empty($companyData->company_name) ? $companyData->company_name : "" ?>" class="form-control" name="companyName" id="companyName">
                                    <input type="hidden" data-name="CSRF Token" name="csrfToken" id="csrfToken" value="<?= session()->csrf_token ?>">
                                </div>
                                <div class="form-group">
                                    <label for="companyDocument">CNPJ</label>
                                    <input type="text" data-name="CNPJ" value="<?= !empty($companyData->company_document) ? $companyData->company_document : "" ?>" data-mask="cnpj" class="form-control" name="companyDocument" id="companyDocument">
                                </div>
                                <div class="form-group">
                                    <label for="stateRegistration">Inscrição estadual</label>
                                    <input type="text" data-name="Inscrição estadual" value="<?= !empty($companyData->state_registration) ? $companyData->state_registration : "" ?>" data-mask="inscricaoEstadual" class="form-control" name="stateRegistration" id="stateRegistration">
                                </div>
                                <div class="form-group">
                                    <label for="companyZipcode">CEP</label>
                                    <input type="text" data-name="CEP" value="<?= !empty($companyData->company_zipcode) ? $companyData->company_zipcode : "" ?>" data-mask="cep" class="form-control" name="companyZipcode" id="companyZipcode">
                                </div>
                                <div class="form-group">
                                    <label for="companyAddress">Endereço</label>
                                    <input type="text" data-name="Endereço" value="<?= !empty($companyData->company_address) ? $companyData->company_address : "" ?>" class="form-control" name="companyAddress" id="companyAddress">
                                </div>
                                <div class="form-group">
                                    <label for="companyAddressNumber">Número</label>
                                    <input type="text" data-name="Número" value="<?= !empty($companyData->company_address_number) ? $companyData->company_address_number : "" ?>" data-mask="number" class="form-control" name="companyAddressNumber" id="companyAddressNumber">
                                </div>
                                <div class="form-group">
                                    <label for="companyNeighborhood">Bairro</label>
                                    <input type="text" data-name="Bairro" value="<?= !empty($companyData->company_neighborhood) ? $companyData->company_neighborhood : "" ?>" class="form-control" name="companyNeighborhood" id="companyNeighborhood">
                                </div>
                                <div class="form-group">
                                    <label for="companyCity">Cidade</label>
                                    <input type="text" data-name="Cidade" value="<?= !empty($companyData->company_city) ? $companyData->company_city : "" ?>" class="form-control" name="companyCity" id="companyCity">
                                </div>
                                <div class="form-group">
                                    <label for="companyState">Estado</label>
                                    <input type="text" data-name="Estado" value="<?= !empty($companyData->company_state) ? $companyData->company_state : "" ?>" data-mask="state" class="form-control" name="companyState" id="companyState">
                                </div>
                                <div class="form-group">
                                    <label for="companyPhone">Telefone</label>
                                    <input type="text" data-name="Telefone" data-notrequired="true" value="<?= !empty($companyData->company_phone) ? $companyData->company_phone : "" ?>" data-mask="phone" class="form-control" name="companyPhone" id="companyPhone">
                                </div>
                                <div class="form-group">
                                    <label for="natureOperation">Natureza da operação</label>
                                    <input type="text" data-name="Natureza da operação" class="form-control" name="natureOperation" id="natureOperation">
                                </div>
                                <div class="form-group">
                                    <label for="invoiceNumber">Número da nota fiscal (9 digitos obrigatório)</label>
                                    <input type="text" data-name="Número da nota fiscal" data-mask="invoiceNumber" class="form-control" name="invoiceNumber" id="invoiceNumber">
                                </div>
                                <div class="form-group">
                                    <label for="invoiceSeries">Série da nota</label>
                                    <input type="text" data-name="Série da nota" data-mask="invoiceSeries" class="form-control" name="invoiceSeries" id="invoiceSeries">
                                </div>
                                <div class="form-group">
                                    <label for="invoiceType">Tipo de nota fiscal</label>
                                    <select name="invoiceType" data-name="Tipo de nota fiscal" id="invoiceType" class="form-control">
                                        <option value="" disabled selected>Selecione o tipo de nota fiscal</option>
                                        <option value="0">Entrada</option>
                                        <option value="1">Saída</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="idInvoiceOperation">Identificador do destino da operação</label>
                                    <select name="idInvoiceOperation" data-name="Identificador do destino da operação" id="idInvoiceOperation" class="form-control">
                                        <option value="" disabled selected>Selecione o tipo de nota fiscal</option>
                                        <option value="1">Operação interna (dentro do mesmo estado)</option>
                                        <option value="2">Operação interestadual</option>
                                        <option value="3">Operação com o exterior</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="municipalityInvoice">Município</label>
                                    <select name="municipalityInvoice" data-name="Município" id="municipalityInvoice" class="form-control">
                                        <option value="" disabled selected>Selecione um Município</option>
                                        <?php if (!empty($responseMunicipality)) : ?>
                                            <?php foreach($responseMunicipality as $municipality) : ?>
                                                <option value="<?= $municipality["id"] ?>"><?= $municipality["name"] ?></option>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="fileInput" class="form-label">Selecione um certificado PFX:</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" data-name="Certificado PFX" name="pfxFile" class="custom-file-input" id="fileInput" accept=".pfx">
                                            <label for="pfxFile" class="custom-file-label">Nome do arquivo</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="certPassword">Senha do certificado</label>
                                    <input type="password" data-name="Senha do certificado" class="form-control" name="certPassword" id="certPassword">
                                </div>
                                <div class="form-group">
                                    <label for="environment">Ambiente</label>
                                    <select name="environment" data-name="Ambiente" id="environment" class="form-control">
                                        <option value="" disabled selected>Selecione um ambiente</option>
                                        <option value="1">Produção</option>
                                        <option value="2">Homologação</option>
                                    </select>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Emitir Nota Fiscal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>