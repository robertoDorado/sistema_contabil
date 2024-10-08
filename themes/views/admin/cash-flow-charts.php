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
            <div class="row mt-5 mb-5">
                <div class="col-12">
                    <div id="containerChartLine" style="display:none">
                        <canvas id="lineChartCashFlowReport" width="550" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="row mt-5 mb-5">
                <div class="col-md-12">
                    <div id="containerPieChart" style="display:none">
                        <canvas style="margin:0 auto" id="pieChartCashFlowReport" width="550" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="row  mt-5 mb-5">
                <div class="col-md-12">
                    <canvas id="monthlyComparasion" width="550" height="200"></canvas>
                </div>
            </div>
            <div class="row  mt-5 mb-5">
                <div class="col-md-12">
                    <canvas id="expenseCategories" width="550" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>