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
                                        <div class="card-body">
                                            Uma das características distintivas do nosso Sistema de Gestão Contábil 
                                            é a robusta funcionalidade de Fluxo de Caixa. Essencial para uma gestão 
                                            financeira eficiente, nossa plataforma oferece um painel intuitivo que 
                                            permite uma visão holística e detalhada do fluxo de entrada e saída de 
                                            recursos financeiros.
                                            <ul>
                                                <li>
                                                    <strong>Previsões Precisas:</strong>
                                                    Antecipe-se às necessidades financeiras da sua empresa com previsões 
                                                    de fluxo de caixa precisas. Nosso sistema utiliza algoritmos avançados 
                                                    para analisar dados históricos e tendências, proporcionando uma projeção 
                                                    confiável das finanças futuras.
                                                </li>
                                                <li>
                                                    <strong>Controle de Despesas e Receitas:</strong>
                                                    Mantenha um controle rigoroso sobre todas as despesas e receitas. 
                                                    Nossa plataforma categoriza automaticamente as transações, 
                                                    simplificando a identificação de áreas de oportunidade para redução de custos
                                                    ou aumento de receitas.
                                                </li>
                                                <li>
                                                    <strong>Tomada de Decisões Estratégicas:</strong>
                                                    Capacite sua equipe de gestão com informações em tempo real. 
                                                    O acesso fácil e rápido ao fluxo de caixa permite uma tomada de decisões 
                                                    estratégicas fundamentada, auxiliando na alocação eficiente de recursos 
                                                    e na identificação de oportunidades de investimento.
                                                </li>
                                                <li>
                                                    <strong>Alertas Automáticos:</strong>
                                                    Receba alertas automáticos para eventos críticos, 
                                                    como saldos baixos ou previsões de fluxo de caixa negativas. 
                                                    Essa funcionalidade proativa ajuda a evitar surpresas financeiras indesejadas 
                                                    e a implementar medidas corretivas antes que problemas se agravem.
                                                </li>
                                            </ul>
                                            O Fluxo de Caixa Dinâmico do nosso sistema não apenas simplifica a gestão diária das finanças, 
                                            mas também se torna uma ferramenta estratégica vital para o crescimento sustentável 
                                            da sua empresa. Tenha o controle total das finanças e transforme dados em ações, 
                                            impulsionando o sucesso financeiro do seu negócio.
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