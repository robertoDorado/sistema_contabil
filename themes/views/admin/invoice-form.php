<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Emissão de NF-e</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Página Inicial</a></li>
                        <li class="breadcrumb-item active">Emissão de NF-e</li>
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
                            <h3 class="card-title">Emitir uma nova NF-e</h3>
                        </div>
                        <div class="wizard" id="form-wizard">
                            <ul class="nav nav-pills mt-4 ml-4">
                                <li class="nav-item"><a class="nav-link active" href="#step1" data-toggle="tab">Identificação da NF-e</a></li>
                                <li class="nav-item"><a class="nav-link" href="#step2" data-toggle="tab">Identificação do emitente</a></li>
                                <li class="nav-item"><a class="nav-link" href="#step3" data-toggle="tab">Endereço do emitente</a></li>
                                <li class="nav-item"><a class="nav-link" href="#step4" data-toggle="tab">Identificação do destinatário</a></li>
                                <li class="nav-item"><a class="nav-link" href="#step5" data-toggle="tab">Endereço do destinatário</a></li>
                                <li class="nav-item"><a class="nav-link" href="#step6" data-toggle="tab">Dados do produto ou serviço</a></li>
                                <li class="nav-item"><a class="nav-link" href="#step7" data-toggle="tab">Confirmação</a></li>
                            </ul>
                            <form id="invoiceForm">
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="step1">
                                            <h3>Identificação da NF-e</h3>
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
                                                <label for="purposeOfIssuance">Finalidade da emissão</label>
                                                <select name="purposeOfIssuance" data-name="Finalidade da emissão" id="purposeOfIssuance" class="form-control">
                                                    <option value="" disabled selected>Selecione a finalidade da emissão</option>
                                                    <option value="1">NFe normal</option>
                                                    <option value="2">NFe complementar</option>
                                                    <option value="3">NFe de ajuste</option>
                                                    <option value="4">Devolução de mercadoria</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="finalConsumer">Consumidor final</label>
                                                <select name="finalConsumer" data-name="Consumidor final" id="finalConsumer" class="form-control">
                                                    <option value="" disabled selected>Selecione se esta nota está direcionada para um consumidor final</option>
                                                    <option value="1">Sim</option>
                                                    <option value="0">Não</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="buyersPresence">Presença do comprador</label>
                                                <select name="buyersPresence" data-name="Presença do comprador" id="buyersPresence" class="form-control">
                                                    <option value="" disabled selected>Selecione se esta nota possui a presença do comprador</option>
                                                    <option value="0">Não se aplica (ex.: fora de uma operação com consumidor final)</option>
                                                    <option value="1">Operação presencial</option>
                                                    <option value="2">Operação não presencial, pela internet</option>
                                                    <option value="3">Operação não presencial, teleatendimento</option>
                                                    <option value="4">NFC-e entrega em domicílio</option>
                                                    <option value="9">Operação não presencial, outros</option>
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
                                        <div class="tab-pane" id="step2">
                                            <h3>Identificação do emitente</h3>
                                            <div class="form-group">
                                                <label for="companyName">Razão Social</label>
                                                <input type="text" data-name="Razão Social" value="<?= !empty($companyData->company_name) ? $companyData->company_name : "" ?>" class="form-control" name="companyName" id="companyName">
                                                <input type="hidden" data-name="CSRF Token" name="csrfToken" id="csrfToken" value="<?= session()->csrf_token ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="fantasyName">Nome fantasia</label>
                                                <input type="text" data-name="Nome fantasia" value="<?= !empty($companyData->company_name) ? $companyData->company_name : "" ?>" class="form-control" name="fantasyName" id="fantasyName">
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
                                                <label for="cnaeInformation">CNAE</label>
                                                <input type="text" data-notrequired="true" data-name="CNAE" data-mask="CNAE" class="form-control" name="cnaeInformation" id="cnaeInformation">
                                            </div>
                                            <div class="form-group">
                                                <label for="companyTaxRegime">Regime tributário da empresa</label>
                                                <select name="companyTaxRegime" data-name="Regime tributário da empresa" id="companyTaxRegime" class="form-control">
                                                    <option value="" disabled selected>Selecione o regime tributário da empresa</option>
                                                    <option value="1">Simples Nacional</option>
                                                    <option value="2">Simples Nacional - Excesso de Sublimite de Receita Bruta</option>
                                                    <option value="3">Regime Normal</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="step3">
                                            <h3>Endereço do emitente</h3>
                                            <div class="form-group">
                                                <label for="companyZipcode">CEP</label>
                                                <input type="text" data-addresstype="issuer" data-name="CEP" value="<?= !empty($companyData->company_zipcode) ? $companyData->company_zipcode : "" ?>" data-mask="cep" class="form-control" name="companyZipcode" id="companyZipcode">
                                            </div>
                                            <div class="form-group">
                                                <label for="companyAddress">Endereço</label>
                                                <input type="text" data-name="Endereço" value="<?= !empty($companyData->company_address) ? $companyData->company_address : "" ?>" class="form-control" name="companyAddress" id="companyAddress">
                                            </div>
                                            <div class="form-group">
                                                <label for="companyComplement">Complemento</label>
                                                <input type="text" data-name="Complemento" class="form-control" name="companyComplement" id="companyComplement">
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
                                                <label for="companyState">Estado</label>
                                                <input type="text" data-name="Estado" value="<?= !empty($companyData->company_state) ? $companyData->company_state : "" ?>" data-mask="state" class="form-control" name="companyState" id="companyState">
                                            </div>
                                            <div class="form-group">
                                                <label for="companyContactType">Tipo de Contato</label>
                                                <select name="companyContactType" data-name="Tipo de Contato" id="companyContactType" class="form-control">
                                                    <option value="1">Telefone</option>
                                                    <option value="2">Celular</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="companyPhone">Telefone</label>
                                                <input type="text" data-name="Telefone" data-notrequired="true" value="<?= !empty($companyData->company_phone) ? $companyData->company_phone : "" ?>" data-mask="phone" class="form-control" name="companyPhone" id="companyPhone">
                                            </div>
                                            <div class="form-group">
                                                <label for="municipalityInvoice">Município</label>
                                                <select name="municipalityInvoice" data-name="Município" id="municipalityInvoice" class="form-control">
                                                    <option value="" disabled selected>Selecione um Município</option>
                                                    <?php if (!empty($responseMunicipality)) : ?>
                                                        <?php foreach ($responseMunicipality as $municipality) : ?>
                                                            <option value="<?= $municipality["id"] ?>"><?= $municipality["name"] ?></option>
                                                        <?php endforeach ?>
                                                    <?php endif ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="step4">
                                            <h3>Identificação do destinatário</h3>
                                            <div class="form-group">
                                                <label for="recipientName">Nome do destinatário</label>
                                                <input type="text" data-name="Nome do destinatário" class="form-control" name="recipientName" id="recipientName">
                                            </div>
                                            <div class="form-group">
                                                <label for="recipientStateRegistrationIndicator">Indicador da inscrição estadual do destinatário</label>
                                                <select name="recipientStateRegistrationIndicator" data-name="Indicador da inscrição estadual do destinatário" id="recipientStateRegistrationIndicator" class="form-control">
                                                    <option value="" disabled selected>Selecione o indicador da inscrição estadual do destinatário</option>
                                                    <option value="1">Contribuinte de ICMS (informar a IE)</option>
                                                    <option value="2">Contribuinte isento de IE</option>
                                                    <option value="9">Não Contribuinte, que pode ou não possuir IE</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="recipientStateRegistration">Inscrição estadual do destinatário</label>
                                                <input type="text" data-notrequired="true" data-name="Inscrição estadual do destinatário" data-mask="inscricaoEstadual" class="form-control" name="recipientStateRegistration" id="recipientStateRegistration">
                                            </div>
                                            <div class="form-group">
                                                <label for="recipientEmail">E-mail do destinatário</label>
                                                <input type="text" data-name="E-mail do destinatário" class="form-control" name="recipientEmail" id="recipientEmail">
                                            </div>
                                            <div class="form-group">
                                                <label for="recipientDocumentType">Tipo de documento do destinatário</label>
                                                <select name="recipientDocumentType" data-name="Tipo de documento do destinatário" id="recipientDocumentType" class="form-control">
                                                    <option value="1">CPF</option>
                                                    <option value="2">CNPJ</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="recipientDocument">Documento do destinatário</label>
                                                <input type="text" data-name="Documento do destinatário" data-mask="cpf" class="form-control" name="recipientDocument" id="recipientDocument">
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="step5">
                                            <h3>Endereço do destinatário</h3>
                                            <div class="form-group">
                                                <label for="recipientZipcode">CEP</label>
                                                <input type="text" data-addresstype="recipient" data-name="CEP" data-mask="cep" class="form-control" name="recipientZipcode" id="recipientZipcode">
                                            </div>
                                            <div class="form-group">
                                                <label for="recipientAddress">Endereço</label>
                                                <input type="text" data-name="Endereço" class="form-control" name="recipientAddress" id="recipientAddress">
                                            </div>
                                            <div class="form-group">
                                                <label for="recipientAddressNumber">Número</label>
                                                <input type="text" data-name="Número" data-mask="number" class="form-control" name="recipientAddressNumber" id="recipientAddressNumber">
                                            </div>
                                            <div class="form-group">
                                                <label for="recipientComplement">Complemento</label>
                                                <input type="text" data-name="Complemento" class="form-control" name="recipientComplement" id="recipientComplement">
                                            </div>
                                            <div class="form-group">
                                                <label for="recipientNeighborhood">Bairro</label>
                                                <input type="text" data-name="Bairro" class="form-control" name="recipientNeighborhood" id="recipientNeighborhood">
                                            </div>
                                            <div class="form-group">
                                                <label for="recipientMunicipality">Município</label>
                                                <select name="recipientMunicipality" data-name="Município" id="recipientMunicipality" class="form-control">
                                                    <option value="" disabled selected>Selecione um Município</option>
                                                    <?php if (!empty($responseMunicipality)) : ?>
                                                        <?php foreach ($responseMunicipality as $municipality) : ?>
                                                            <option value="<?= $municipality["id"] ?>"><?= $municipality["name"] ?></option>
                                                        <?php endforeach ?>
                                                    <?php endif ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="recipientState">Estado</label>
                                                <input type="text" data-mask="state" data-name="Estado" class="form-control" name="recipientState" id="recipientState">
                                            </div>
                                            <div class="form-group">
                                                <label for="recipientContactType">Tipo de Contato</label>
                                                <select name="recipientContactType" data-name="Tipo de Contato" id="recipientContactType" class="form-control">
                                                    <option value="1">Telefone</option>
                                                    <option value="2">Celular</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="recipientPhone">Telefone</label>
                                                <input type="text" data-mask="phone" data-name="Telefone" class="form-control" name="recipientPhone" id="recipientPhone">
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="step6">
                                            <h3>Dados do produto ou serviço</h3>
                                            <div class="form-group">
                                                <label for="productItem">Número do item sequencial</label>
                                                <input type="text" data-mask="number" data-name="Número do item sequencial" class="form-control" name="productItem" id="productItem">
                                            </div>
                                            <div class="form-group">
                                                <label for="productCode">Código do produto</label>
                                                <input type="text" data-notrequired="true" data-name="Código do produto" class="form-control" name="productCode" id="productCode">
                                            </div>
                                            <div class="form-group">
                                                <label for="barCodeProduct">Código de barras do produto (GTIN)</label>
                                                <input type="text" data-notrequired="true" data-name="Código de barras do produto" class="form-control" name="barCodeProduct" id="barCodeProduct">
                                            </div>
                                            <div class="form-group">
                                                <label for="additionalBarCodeProduct">Código de barras adicional do produto</label>
                                                <input type="text"  data-notrequired="true" data-name="Código de barras adicional do produto" class="form-control" name="additionalBarCodeProduct" id="additionalBarCodeProduct">
                                            </div>
                                            <div class="form-group">
                                                <label for="productDescription">Descrição do produto</label>
                                                <input type="text" data-name="Descrição do produto" class="form-control" name="productDescription" id="productDescription">
                                            </div>
                                            <div class="form-group">
                                                <label for="productNcmCode">Código NCM (Nomenclatura Comum do Mercosul)</label>
                                                <input type="text" data-mask="number" data-name="Código NCM (Nomenclatura Comum do Mercosul)" class="form-control" name="productNcmCode" id="productNcmCode">
                                            </div>
                                            <div class="form-group">
                                                <label for="productCodeBenef">Código de benefício fiscal</label>
                                                <input type="text" data-notrequired="true" data-mask="number" data-name="Código de benefício fiscal" class="form-control" name="productCodeBenef" id="productCodeBenef">
                                            </div>
                                            <div class="form-group">
                                                <label for="productCodeTipi">Código TIPI - identificador de exceções na tabela de incidência do imposto sobre produtos industrializados (TIPI)</label>
                                                <input type="text" data-notrequired="true" data-mask="number" data-name="Código TIPI" class="form-control" name="productCodeTipi" id="productCodeTipi">
                                            </div>
                                            <div class="form-group">
                                                <label for="productCodeCfop">Código Fiscal de Operações e Prestações</label>
                                                <input type="text" data-mask="number" data-name="Código Fiscal de Operações e Prestações" class="form-control" name="productCodeCfop" id="productCodeCfop">
                                            </div>
                                            <div class="form-group">
                                                <label for="productComercialUnit">Unidade comercial</label>
                                                <input type="text" data-name="Unidade comercial" class="form-control" name="productComercialUnit" id="productComercialUnit">
                                            </div>
                                            <div class="form-group">
                                                <label for="qttyProduct">Quantidade do produto</label>
                                                <input type="text" data-mask="number" data-name="Quantidade do produto" class="form-control" name="qttyProduct" id="qttyProduct">
                                            </div>
                                            <div class="form-group">
                                                <label for="productValueUnit">Valor unitário do produto</label>
                                                <input type="text" data-mask="number" data-name="Valor unitário do produto" class="form-control" name="productValueUnit" id="productValueUnit">
                                            </div>
                                            <div class="form-group">
                                                <label for="productTotalValue">Valor total do produto</label>
                                                <input type="text" data-mask="number" data-name="Valor total do produto" class="form-control" name="productTotalValue" id="productTotalValue">
                                            </div>
                                            <div class="form-group">
                                                <label for="barCodeProductTrib">Código de barras do produto tributado (GTIN)</label>
                                                <input type="text" data-notrequired="true" data-name="Código de barras do produto tributado (GTIN)" class="form-control" name="barCodeProductTrib" id="barCodeProductTrib">
                                            </div>
                                            <div class="form-group">
                                                <label for="additionalBarCodeProductTrib">Código de barras adicional para o produto tributado</label>
                                                <input type="text" data-notrequired="true" data-name="Código de barras adicional para o produto tributado" class="form-control" name="additionalBarCodeProductTrib" id="additionalBarCodeProductTrib">
                                            </div>
                                            <div class="form-group">
                                                <label for="productTaxUnit">Unidade de tributação</label>
                                                <input type="text" data-name="Unidade de tributação" class="form-control" name="productTaxUnit" id="productTaxUnit">
                                            </div>
                                            <div class="form-group">
                                                <label for="qttyProuctTax">Quantidade tributável</label>
                                                <input type="text" data-mask="number" data-name="Quantidade tributável" class="form-control" name="qttyProuctTax" id="qttyProuctTax">
                                            </div>
                                            <div class="form-group">
                                                <label for="taxUnitValue">Valor unitário tributável</label>
                                                <input type="text" data-mask="number" data-name="Valor unitário tributável" class="form-control" name="taxUnitValue" id="taxUnitValue">
                                            </div>
                                            <div class="form-group">
                                                <label for="productShippingValue">Valor do frete</label>
                                                <input type="text" data-notrequired="true" data-mask="number" data-name="Valor do frete" class="form-control" name="productShippingValue" id="productShippingValue">
                                            </div>
                                            <div class="form-group">
                                                <label for="productInsuranceValue">Valor do seguro</label>
                                                <input type="text" data-notrequired="true" data-mask="number" data-name="Valor do seguro" class="form-control" name="productInsuranceValue" id="productInsuranceValue">
                                            </div>
                                            <div class="form-group">
                                                <label for="productDiscountAmount">Valor do desconto</label>
                                                <input type="text" data-notrequired="true" data-mask="number" data-name="Valor do desconto" class="form-control" name="productDiscountAmount" id="productDiscountAmount">
                                            </div>
                                            <div class="form-group">
                                                <label for="productValueOtherExpenses">Valor de outras despesas acessórias</label>
                                                <input type="text" data-notrequired="true" data-mask="number" data-name="Valor de outras despesas acessórias" class="form-control" name="productValueOtherExpenses" id="productValueOtherExpenses">
                                            </div>
                                            <div class="form-group">
                                                <label for="productOrderNumber">Número do pedido de compra</label>
                                                <input type="text" data-notrequired="true" data-name="Número do pedido de compra" class="form-control" name="productOrderNumber" id="productOrderNumber">
                                            </div>
                                            <div class="form-group">
                                                <label for="productItemNumberBuyOrder">Número do item do pedido de compra</label>
                                                <input type="text" data-mask="number" data-notrequired="true" data-name="Número do item do pedido de compra" class="form-control" name="productItemNumberBuyOrder" id="productItemNumberBuyOrder">
                                            </div>
                                            <div class="form-group">
                                                <label for="fciNumber">Número do FCI (Ficha de Conteúdo de Importação)</label>
                                                <input type="text" data-notrequired="true" data-name="Número do FCI (Ficha de Conteúdo de Importação)" class="form-control" name="fciNumber" id="fciNumber">
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="step7">
                                            <h3>Confirmação</h3>
                                            <p>Revise as informações antes de emitir a nota fiscal.</p>
                                            <div class="card-footer">
                                                <button type="submit" class="btn btn-primary">Emitir Nota Fiscal</button>
                                            </div>
                                        </div>
                                        <ul class="pager wizard mt-4 container-page-wizard">
                                            <li class="previous"><a class="btn btn-secondary" href="#">Anterior</a></li>
                                            <li class="next"><a class="btn btn-primary" href="#">Próximo</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>