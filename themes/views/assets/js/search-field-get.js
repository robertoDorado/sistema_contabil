const endpointsSearchCashFlowGet = [
    `/admin/cash-flow/report${window.location.search}`,
    `/admin/analyzes-and-indicators/cash-flow/charts-and-visualizations${window.location.search}`,
    `/admin/analyzes-and-indicators/cash-flow/cash-flow-projections${window.location.search}`,
    `/admin/cash-planning/cash-flow/cash-budget${window.location.search}`,
    `/admin/cash-planning/cash-flow/cash-variation-analysis${window.location.search}`,
    `/admin/balance-sheet/balance-sheet-overview/report${window.location.search}`,
    `/admin/balance-sheet/daily-journal/report${window.location.search}`,
    `/admin/balance-sheet/trial-balance/report${window.location.search}`
]

const currentUrlSearchCashFlowGet = window.location.pathname + window.location.search
if (endpointsSearchCashFlowGet.indexOf(currentUrlSearchCashFlowGet) != -1) {
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

    const searchByDate = document.getElementById("searchByDate")
    searchByDate.addEventListener("submit", function(event) {
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