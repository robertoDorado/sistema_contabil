<?php $v->layout("admin/layouts/_admin") ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Página inicial</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Home</a></li>
                        <li class="breadcrumb-item active">Página Inicial</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- /.col-md-6 -->
                <div class="col-lg-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="m-0">Apresentação do Sistema de Gestão Contábil</h5>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">Seja bem-vindo ao futuro da gestão contábil!</h6>
                            <p class="card-text">Apresentamos nosso avançado Sistema de Gestão Contábil,
                                uma solução integrada projetada para otimizar e simplificar a complexidade
                                do universo financeiro das empresas.</p>
                            <div id="accordion">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h4 class="card-title w-100">
                                            <a class="d-block w-100" data-toggle="collapse" href="#collapseOne">
                                                Fluxo de Caixa Dinâmico
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                                        <div class="card-body documentation">
                                            <h6><strong>Documentação</strong></h6>
                                            <p>
                                                O menu "Fluxo de Caixa" no sistema contábil é uma ferramenta essencial
                                                para gerenciar, monitorar e analisar o movimento de caixa da empresa.
                                                Ele fornece informações detalhadas sobre as entradas e saídas de caixa,
                                                ajudando a empresa a manter a liquidez e a planejar suas finanças.
                                                A seguir, é apresentada uma documentação detalhada das funcionalidades
                                                e submenus do menu "Fluxo de Caixa".
                                            </p>
                                            <h6><strong>Estrutura do Menu Fluxo de Caixa</strong></h6>
                                            <ol>
                                                <li>Visão Geral do Fluxo de Caixa</li>
                                                <li>Configuração do Fluxo de Caixa</li>
                                                <li>Lançamentos de Caixa</li>
                                                <li>Backup de contas</li>
                                                <li>Conciliação Bancária</li>
                                                <li>Análises e Indicadores</li>
                                                <li>Configuração Variação de caixa</li>
                                                <li>Planejamento de Caixa</li>
                                                <li>Notas Explicativas</li>
                                                <li>Auditoria e Histórico</li>
                                            </ol>
                                            <h6><strong>Visão Geral do Fluxo de Caixa</strong></h6>
                                            <h6><strong>Funcionalidades:</strong></h6>
                                            <ul>
                                                <li><strong>Painel Resumido:</strong> Exibição de um resumo das entradas e saídas de caixa para o período selecionado.</li>
                                                <li><strong>Saldos de Caixa:</strong> Mostra o saldo inicial, entradas totais, saídas totais e saldo final.</li>
                                                <li><strong>Importação de Lançamentos:</strong> Importação de transações de caixa a partir de arquivos Excel, extensão .xlsx</li>
                                                <li><strong>Filtro por data:</strong> Filtro por data de lançamento da conta.</li>
                                                <li><strong>Campo de busca:</strong> Campo de busca genérico por qualquer item que existe no relatório</li>
                                                <li><strong>Editar/Excluir Lançamentos:</strong> Ferramentas para editar ou excluir lançamentos existentes.</li>
                                            </ul>
                                            <h6><strong>Configuração do Fluxo de Caixa</strong></h6>
                                            <h6><strong>Funcionalidades:</strong></h6>
                                            <ul>
                                                <li><strong>Lançar grupo de contas:</strong> Lançamento do nome do grupo de contas para agrupamento do fluxo de caixa.</li>
                                                <li><strong>Editar/Excluir Lançamentos:</strong> Ferramentas para editar ou excluir grupo de contas existentes.</li>
                                                <li><strong>Campo de busca:</strong> Campo de busca genérico por qualquer item que existe no relatório</li>
                                                <li><strong>Relatório grupo de contas:</strong> Demonstrativo de todos os grupos de contas ativos para a empresa selecionada.</li>
                                                <li><strong>Backup grupo de contas:</strong> Demonstrativo de todos os grupos de contas que foram excluídos para a empresa selecionada. Possibilitando o usuário de restaurar a conta ou excluir permanentemente.</li>
                                            </ul>
                                            <h6><strong>Lançamentos de Caixa</strong></h6>
                                            <h6><strong>Funcionalidades:</strong></h6>
                                            <ul>
                                                <li><strong>Novo Lançamento:</strong> Registro de novas transações de caixa, incluindo data, valor, categoria e descrição.</li>
                                            </ul>
                                            <h6><strong>Backup de contas</strong></h6>
                                            <h6><strong>Funcionalidades:</strong></h6>
                                            <ul>
                                                <li><strong>Relatório backup de contas:</strong> Demonstrativo de todas as contas que foram excluídas para a empresa selecionada. Possibilitando o usuário de restaurar a conta ou excluir permanentemente.</li>
                                                <li><strong>Campo de busca:</strong> Campo de busca genérico por qualquer item que existe no relatório</li>
                                            </ul>
                                            <h6><strong>Conciliação bancária</strong></h6>
                                            <h6><strong>Funcionalidades:</strong></h6>
                                            <ul>
                                                <li><strong>Importar Extratos Bancários:</strong> Importação de extratos bancários no formato OFX.</li>
                                                <li><strong>Conciliação Automática:</strong> Ferramentas para conciliação automática das transações bancárias com os registros internos.</li>
                                                <li><strong>Conciliação Manual:</strong> Interface para conciliação manual das transações, permitindo associar manualmente transações bancárias com registros internos.</li>
                                                <li><strong>Relatórios de Conciliação:</strong> Geração de relatórios detalhados mostrando as transações conciliadas e não conciliadas.</li>
                                            </ul>
                                            <h6><strong>Análises e Indicadores</strong></h6>
                                            <h6><strong>Funcionalidades:</strong></h6>
                                            <ul>
                                                <li><strong>Projeções de caixa:</strong> Previsão de entradas e saídas de dinheiro em um determinado período futuro, possiblitando filtrar por período.</li>
                                                <li><strong>Indicadores Financeiros:</strong> Cálculo e exibição de indicadores financeiros. 
                                                Abaixo a lista dos indicadores financeiros que devem estar cadastrados no grupo de contas para serem exibidas no relatório.</li>
                                                <?php if (!empty(financialIndicators())) : ?>
                                                    <ol>
                                                        <?php foreach(financialIndicators() as $value): ?>
                                                            <li><?= strtoupper(substr($value, 0, 1)) . substr($value, 1) ?></li>
                                                        <?php endforeach ?>
                                                    </ol>
                                                <?php endif ?>
                                                <li><strong>Gráficos e visualizações:</strong> Gráficos de barras ou linhas que mostram as tendências de entradas e saídas de caixa ao longo do tempo.</li>
                                            </ul>
                                            <h6><strong>Configuração Variação de Caixa</strong></h6>
                                            <h6><strong>Funcionalidades:</strong></h6>
                                            <ul>
                                                <li><strong>Lançar variação de caixa:</strong> Lançamento de grupo de contas para agrupamento de variação de caixa. Abaixo as variações de caixa que exercem o agrupamento das contas.</li>
                                                <ol>
                                                    <li>Fluxo de caixa operacional</li>
                                                    <li>Fluxo de caixa de investimento</li>
                                                    <li>Fluxo de caixa de financiamento</li>
                                                </ol>
                                                <li><strong>Relatório variação de caixa:</strong> Geração de relatórios detalhados mostrando as variações de caixa cadastradas.</li>
                                                <li><strong>Backup variação de caixa:</strong> Demonstrativo de todas variações de caixa que foram excluídas para a empresa selecionada. Possibilitando o usuário de restaurar a conta ou excluir permanentemente.</li>
                                            </ul>
                                            <h6><strong>Planejamento de Caixa</strong></h6>
                                            <h6><strong>Funcionalidades:</strong></h6>
                                            <ul>
                                                <li><strong>Orçamentos de Caixa:</strong> Ferramenta para análise orçamentos de caixa possibilitando filtros por período.</li>
                                                <li><strong>Análise de Variação:</strong> Relatório para análise de atividades operacionais, atividades de investimento e atividades de financiamento.</li>
                                            </ul>
                                            <h6><strong>Notas Explicativas</strong></h6>
                                            <h6><strong>Funcionalidades:</strong></h6>
                                            <ul>
                                                <li><strong>Adicionar Notas:</strong> Inserção de notas explicativas para transações específicas.</li>
                                                <li><strong>Editar/Excluir Notas:</strong> Ferramentas para editar ou excluir notas existentes.</li>
                                                <li><strong>Visualizar Notas:</strong> Exibição de notas explicativas associadas a transações no relatório de fluxo de caixa.</li>
                                                <li><strong>Backup de Notas:</strong> Demonstrativo de todas as notas explicativas que foram excluídas para a empresa selecionada. Possibilitando o usuário de restaurar a conta ou excluir permanentemente.</li>
                                            </ul>
                                            <h6><strong>Auditoria e Históricos</strong></h6>
                                            <h6><strong>Funcionalidades:</strong></h6>
                                            <ul>
                                                <li><strong>Registro de Atividades:</strong> Histórico detalhado de todas as atividades relacionadas ao fluxo de caixa, incluindo criação, edição, exclusão e importação das transações.</li>
                                                <li><strong>Relatórios de Auditoria:</strong> Geração de relatórios detalhados para auditoria interna e externa, mostrando todas as alterações e atividades no fluxo de caixa.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col-md-6 -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->