<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Formulário de atualização livro diário</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin/balance-sheet/daily-journal/report") ?>">Relatório livro diário</a></li>
                        <li class="breadcrumb-item active">Formulário de atualização livro diário</li>
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
                            <h3 class="card-title">Fazer uma atualização</h3>
                        </div>
                        <form id="dailyJournalFormUpdate">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="createdAt">Data criada</label>
                                    <input type="text" value="<?= !empty($dailyJournal->created_at) ? $dailyJournal->created_at : "" ?>" name="createdAt" class="form-control" id="createdAt">
                                    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= session()->csrf_token ?>">
                                </div>
                                <div class="form-group">
                                    <label for="chartOfAccountSelect">Nome da conta</label>
                                    <select name="chartOfAccountSelect" id="chartOfAccountSelect" class="form-control">
                                        <option value="" disabled>Selecione o nome da conta</option>
                                        <?php foreach($chartOfAccountData as $accountData): ?>
                                            <option value="<?= $accountData->getUuid() ?>" <?= $dailyJournal->id_chart_of_account == $accountData->id ? "selected" : "" ?>><?= $accountData->account_number . " - " . $accountData->account_name ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="accountType">Tipo de conta</label>
                                    <select name="accountType" id="accountType" class="form-control">
                                        <option value="" disabled>Selecione o tipo de conta</option>
                                        <option value="1" <?= !empty($dailyJournal->account_type) ? "selected" : "" ?>>Crédito</option>
                                        <option value="0" <?= empty($dailyJournal->account_type) ? "selected" : "" ?>>Débito</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="accountValue">Valor de Lançamento</label>
                                    <input type="text" value="<?= !empty($dailyJournal->account_value) ? $dailyJournal->account_value : "" ?>" name="accountValue" class="form-control" id="accountValue">
                                </div>
                                <div class="form-group">
                                    <label for="accountHistory">Histórico da conta</label>
                                    <textarea class="form-control" name="accountHistory" value="<?= !empty($dailyJournal->history_account) ? $dailyJournal->history_account : "" ?>" id="accountHistory"><?= !empty($dailyJournal->history_account) ? $dailyJournal->history_account : "" ?></textarea>
                                </div>
                            </div>
    
                            <div class="card-footer">
                                <button type="submit" id="updateBtn" class="btn btn-primary">Atualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>