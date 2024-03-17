if (window.location.pathname == "/admin/cash-flow/report") {
    $(document).ready(function() {
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
    const tFoot = document.querySelector("tfoot").firstElementChild
    cashFlowTable.on('search.dt', function () {
        const dataFilter = cashFlowTable.rows({ search: 'applied' }).data();
        let balance = 0
        dataFilter.each(function (row) {
            let entryValue = parseFloat(row[4].replace("R$", "")
                .replace(".", "").replace(",", ".").trim())

            balance += entryValue
        })

        balance < 0 ? tFoot.style.color = "#ff0000" : balance == 0 ?
            tFoot.removeAttribute("style") : tFoot.style.color = "#008000"

        tFoot.children[4].innerHTML = balance
            .toLocaleString("pt-br", { "currency": "BRL", "style": "currency" })
    })
}