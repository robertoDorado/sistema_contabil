<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Indicadores financeiros</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Home</a></li>
                        <li class="breadcrumb-item active">Indicadores financeiros</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="content">
        <div class="container-fluid">
            <?php if (!empty($financialIndicators["recebimentos de clientes"]) && !empty($financialIndicators["pagamentos a fornecedores e empregados"])) : ?>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Fluxo de Caixa Operacional (FCO)</h3><br>
                                <small>FCO=Recebimentos de clientes-Pagamentos a fornecedores e empregados</small>
                            </div>
                            <div class="card-body">
                                <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                    <table id="financialIndicatorsFco" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Recebimentos de clientes</th>
                                                <th>Pagamentos a fornecedores e empregados</th>
                                                <th>Resultado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?= "R$ " . number_format($financialIndicators["recebimentos de clientes"], 2, ",", ".") ?></td>
                                                <td><?= "R$ " . number_format($financialIndicators["pagamentos a fornecedores e empregados"], 2, ",", ".") ?></td>
                                                <td><?= "R$ " . number_format($financialIndicators["recebimentos de clientes"] - $financialIndicators["pagamentos a fornecedores e empregados"], 2, ",", ".") ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if (!empty($financialIndicators["fco"]) && !empty($financialIndicators["despesas de capital"])) : ?>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Fluxo de Caixa Livre (FCL)</h3><br>
                                <small>FCL=FCO-Despesas de Capital</small>
                            </div>
                            <div class="card-body">
                                <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                    <table id="financialIndicatorsFcl" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>FCO</th>
                                                <th>Despesas de capital</th>
                                                <th>Resultado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?= "R$ " . number_format($financialIndicators["fco"], 2, ",", ".") ?></td>
                                                <td><?= "R$ " . number_format($financialIndicators["despesas de capital"], 2, ",", ".") ?></td>
                                                <td><?= "R$ " . number_format($financialIndicators["fco"] - $financialIndicators["despesas de capital"], 2, ",", ".") ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if (!empty($financialIndicators["emissão de dívidas ou ações"]) && !empty($financialIndicators["pagamento de dívidas ou dividendos"])) : ?>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Fluxo de Caixa de Financiamento (FCF)</h3><br>
                                <small>FCF=Emissão de dívidas ou ações-Pagamento de dívidas ou dividendos</small>
                            </div>
                            <div class="card-body">
                                <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                    <table id="financialIndicatorsFcf" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Emissão de dívidas ou ações</th>
                                                <th>Pagamento de dívidas ou dividendos</th>
                                                <th>Resultado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?= "R$ " . number_format($financialIndicators["emissão de dívidas ou ações"], 2, ",", ".") ?></td>
                                                <td><?= "R$ " . number_format($financialIndicators["pagamento de dívidas ou dividendos"], 2, ",", ".") ?></td>
                                                <td><?= "R$ " . number_format($financialIndicators["emissão de dívidas ou ações"] - $financialIndicators["pagamento de dívidas ou dividendos"], 2, ",", ".") ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if (!empty($financialIndicators["compra de ativos fixos"]) && !empty($financialIndicators["venda de investimentos"])) : ?>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Fluxo de Caixa de Investimento (FCI)</h3><br>
                                <small>FCI=Compra de Ativos Fixos-Venda de Investimentos</small>
                            </div>
                            <div class="card-body">
                                <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                    <table id="financialIndicatorsFci" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Compra de ativos fixos</th>
                                                <th>Venda de investimentos</th>
                                                <th>Resultado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?= "R$ " . number_format($financialIndicators["compra de ativos fixos"], 2, ",", ".") ?></td>
                                                <td><?= "R$ " . number_format($financialIndicators["venda de investimentos"], 2, ",", ".") ?></td>
                                                <td><?= "R$ " . number_format($financialIndicators["compra de ativos fixos"] - $financialIndicators["venda de investimentos"], 2, ",", ".") ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if (!empty($financialIndicators["fco"]) && !empty($financialIndicators["pagamentos de juros"])) : ?>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Índice de Cobertura de Juros pelo Fluxo de Caixa (ICJFC)</h3><br>
                                <small>ICJFC=(FCO+Pagamentos de juros)/Pagamentos de juros</small>
                            </div>
                            <div class="card-body">
                                <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                    <table id="financialIndicatorsIcjfc" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>FCO</th>
                                                <th>Pagamentos de juros</th>
                                                <th>Resultado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?= "R$ " . number_format($financialIndicators["fco"], 2, ",", ".") ?></td>
                                                <td><?= "R$ " . number_format($financialIndicators["pagamentos de juros"], 2, ",", ".") ?></td>
                                                <td><?= number_format(($financialIndicators["fco"] + $financialIndicators["pagamentos de juros"]) / $financialIndicators["pagamentos de juros"], 2, ",", ".") ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if (!empty($financialIndicators["fco"]) && !empty($financialIndicators["pagamentos de dívidas"])) : ?>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Índice de Cobertura do Serviço da Dívida (ICSD)</h3><br>
                                <small>ICSD=FCO/Pagamentos de dívidas</small>
                            </div>
                            <div class="card-body">
                                <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                    <table id="financialIndicatorsIcsd" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>FCO</th>
                                                <th>Pagamentos de dívidas</th>
                                                <th>Resultado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?= "R$ " . number_format($financialIndicators["fco"], 2, ",", ".") ?></td>
                                                <td><?= "R$ " . number_format($financialIndicators["pagamentos de dívidas"], 2, ",", ".") ?></td>
                                                <td><?= number_format($financialIndicators["fco"] / $financialIndicators["pagamentos de dívidas"], 2, ",", ".") ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if (!empty($financialIndicators["fco"]) && !empty($financialIndicators["lucro líquido"])) : ?>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Índice de Retorno sobre o Fluxo de Caixa (IRFC)</h3><br>
                                <small>IRFC=Lucro líquido/FCO</small>
                            </div>
                            <div class="card-body">
                                <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                    <table id="financialIndicatorsIrfc" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Lucro líquido</th>
                                                <th>FCO</th>
                                                <th>Resultado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?= "R$ " . number_format($financialIndicators["lucro líquido"], 2, ",", ".") ?></td>
                                                <td><?= "R$ " . number_format($financialIndicators["fco"], 2, ",", ".") ?></td>
                                                <td><?= number_format($financialIndicators["lucro líquido"] / $financialIndicators["fco"], 2, ",", ".") ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if (!empty($financialIndicators["fco"]) && !empty($financialIndicators["receita líquida"])) : ?>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Margem de Fluxo de Caixa (MFC)</h3><br>
                                <small>MFC=FCO/Receita líquida</small>
                            </div>
                            <div class="card-body">
                                <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                    <table id="financialIndicatorsMfc" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>FCO</th>
                                                <th>Receita líquida</th>
                                                <th>Resultado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?= "R$ " . number_format($financialIndicators["fco"], 2, ",", ".") ?></td>
                                                <td><?= "R$ " . number_format($financialIndicators["receita líquida"], 2, ",", ".") ?></td>
                                                <td><?= number_format($financialIndicators["fco"] / $financialIndicators["receita líquida"], 2, ",", ".") ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if (!empty($financialIndicators["período médio de cobrança"]) && !empty($financialIndicators["período médio de estoque"]) && !empty($financialIndicators["período médio de pagamento"])) : ?>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Ciclo de caixa</h3><br>
                                <small>Ciclo de caixa=Período médio de cobrança + Período médio de estoque - Período médio de pagamento</small>
                            </div>
                            <div class="card-body">
                                <div id="widgets" class="dataTables_wrapper dt-bootstrap4">
                                    <table id="financialIndicatorsCc" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Período médio de cobrança</th>
                                                <th>Período médio de estoque</th>
                                                <th>Período médio de pagamento</th>
                                                <th>Resultado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?= "R$ " . number_format($financialIndicators["período médio de cobrança"], 2, ",", ".") ?></td>
                                                <td><?= "R$ " . number_format($financialIndicators["período médio de estoque"], 2, ",", ".") ?></td>
                                                <td><?= "R$ " . number_format($financialIndicators["período médio de pagamento"], 2, ",", ".") ?></td>
                                                <td><?= "R$ " . number_format($financialIndicators["período médio de cobrança"] + $financialIndicators["período médio de estoque"] - $financialIndicators["período médio de pagamento"], 2, ",", ".") ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>