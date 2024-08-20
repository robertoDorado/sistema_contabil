<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Atualização de notas explicativas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin/balance-sheet-explanatory-notes/report") ?>">Relatório de notas</a></li>
                        <li class="breadcrumb-item active">Atualização de notas explicativas</li>
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
                            <h3 class="card-title">Atualizar nota</h3>
                        </div>
                        <form id="balanceSheetExplanatoryNotesForm">
                            <div class="card-body">
                                <p class="card-text"><strong>Data:</strong> <?= $explanatoryNotesBalanceSheetData->created_at ?></p>
                                <p class="card-text"><strong>Número da conta:</strong> <?= $explanatoryNotesBalanceSheetData->account_number ?></p>
                                <p class="card-text"><strong>Conta:</strong> <?= $explanatoryNotesBalanceSheetData->account_name ?></p>
                                <p class="card-text"><strong>Descrição:</strong> <?= $explanatoryNotesBalanceSheetData->history_account ?></p>
                                <p class="card-text"><strong>Crédito/Débito:</strong> <?= $explanatoryNotesBalanceSheetData->account_type ?></p>
                                <p class="card-text"><strong>Valor:</strong> <?= $explanatoryNotesBalanceSheetData->account_value ?></p>
                                <div class="form-group">
                                    <label for="explanatoryNoteText">Nota</label>
                                    <textarea value="<?= $explanatoryNotesBalanceSheetData->getNote() ?>" name="explanatoryNoteText" class="form-control" id="explanatoryNoteText"><?= $explanatoryNotesBalanceSheetData->getNote() ?></textarea>
                                    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= session()->csrf_token ?>">
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" id="launchBtn" class="btn btn-primary">Atualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>