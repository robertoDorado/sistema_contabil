<form id="searchByDate" method="get" action="<?= !empty($urlDateRangeInput) ? $urlDateRangeInput : "#" ?>">
    <div class="form-row">
        <div class="col-md-6">
            <label for="date-range">Busca por data:</label>
            <input type="text" value="<?= empty($_GET['daterange']) ? "" : $_GET['daterange'] ?>" name="daterange" id="date-range" class="form-control" />
        </div>
        <div class="col-md-2">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-primary btn-block" id="btn-search">Buscar</button>
        </div>
    </div>
</form>