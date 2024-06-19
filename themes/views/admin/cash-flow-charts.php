<?php $v->layout("admin/layouts/_admin") ?>
<?php $v->insert("admin/layouts/_modal") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gráficos e visualizações</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Home</a></li>
                        <li class="breadcrumb-item active">Gráficos e visualizações</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <?= $v->insert("admin/layouts/_daterange_input") ?>
                </div>
            </div>
            <div class="row">
                <div id="containerChartLine" class="col-md-6 mt-5" style="display:none">
                    <canvas id="lineChartCashFlowReport" width="800" height="400"></canvas>
                </div>
                <div id="containerPieChart" class="col-md-6 mt-5 mb-5" style="display:none">
                    <canvas style="margin:0 auto" id="pieChartCashFlowReport" width="350" height="350"></canvas>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <canvas id="monthlyComparasion"></canvas>
                </div>
                <div class="col-md-6">
                    <canvas id="expenseCategories"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>