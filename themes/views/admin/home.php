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
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
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
                                                Automatização Eficiente
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                                        <div class="card-body">
                                            Nosso sistema utiliza algoritmos inteligentes para automatizar 
                                            tarefas rotineiras, economizando tempo e minimizando erros. 
                                            A automação de processos contábeis permite que você e sua equipe 
                                            foquem em análises estratégicas e tomada de decisões.
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h4 class="card-title w-100">
                                            <a class="d-block w-100" data-toggle="collapse" href="#collapseOne">
                                                Integração Total
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseOne" class="collapse" data-parent="#accordion">
                                        <div class="card-body">
                                            Conectamos todos os aspectos da gestão contábil em uma única 
                                            plataforma. Desde a emissão de notas fiscais até a conciliação 
                                            bancária, nossa solução proporciona uma visão holística e em tempo 
                                            real de todas as operações financeiras da sua empresa.
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