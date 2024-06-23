const allowEndponts = ["/admin/bank-reconciliation/cash-flow/manual", "/admin/cash-flow/report"]
if (allowEndponts.indexOf(window.location.pathname) != -1) {
    const tFoot = document.querySelector("tfoot").firstElementChild
    cashFlowTable.on('search.dt', function () {
        const dataFilter = cashFlowTable.rows({ search: 'applied' }).data();
        let balance = 0
        dataFilter.each(function (row) {
            let entryValue = parseFloat(row[5].replace("R$", "")
                .replace(".", "").replace(",", ".").trim())

            balance += entryValue
        })

        balance < 0 ? tFoot.style.color = "#ff0000" : balance == 0 ?
            tFoot.removeAttribute("style") : tFoot.style.color = "#008000"

        tFoot.children[5].innerHTML = balance
            .toLocaleString("pt-br", { "currency": "BRL", "style": "currency" })
    })
}