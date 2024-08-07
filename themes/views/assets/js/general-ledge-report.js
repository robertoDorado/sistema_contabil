if (window.location.pathname == "/admin/balance-sheet/general-ledge/report") {
    $("#selectChartOfAccountMultiple").select2()

    $(document).ready(function () {
        $('#date-range').daterangepicker({
            opens: 'left',
            locale: {
                format: 'DD/MM/YYYY',
                separator: ' - ',
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
            }
        });
    });

    const gerneralLedgeSearch = document.getElementById("gerneralLedgeSearch")
    gerneralLedgeSearch.addEventListener("submit", function(event) {
        event.preventDefault()
        const btnSubmit = this.querySelector("[type='submit']")
        showSpinner(btnSubmit)
        this.submit()
        setTimeout(function() {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Buscar"
        }, 3000)
    })
}