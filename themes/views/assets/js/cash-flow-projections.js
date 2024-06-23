if (window.location.pathname == "/admin/analyzes-and-indicators/cash-flow/cash-flow-projections") {
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

    document.getElementById("searchCashFlowById").addEventListener("submit", function(event) {
        event.preventDefault()
        showSpinner(this.querySelector("[type='submit']"))
        this.submit()
    })
}