<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Formulário de notas explicativas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Página Inicial</a></li>
                        <li class="breadcrumb-item active">Formulário de notas explicativas</li>
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
                            <h3 class="card-title">Nova nota</h3>
                        </div>
                        <form id="cashFlowExplanatoryNotesForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="explanatoryNoteText">Nota</label>
                                    <textarea name="explanatoryNoteText" class="form-control" id="explanatoryNoteText"></textarea>
                                    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= session()->csrf_token ?>">
                                </div>
                                <div class="form-group">
                                    <select id="cashFlowSelectMultiple" name="cashFlowSelectMultiple[]" multiple="multiple" style="width: 50%">
                                        <?php if (!empty($cashFlowData)) : ?>
                                            <?php foreach($cashFlowData as $cashFlow) : ?>
                                                <option value="<?= $cashFlow->getUuid() ?>"><?= $cashFlow->getHistory() ?></option>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </select>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" id="launchBtn" class="btn btn-primary">Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>