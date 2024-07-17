const allowEndponts = ["/admin/bank-reconciliation/cash-flow/manual", "/admin/cash-flow/report"]
if (allowEndponts.indexOf(window.location.pathname) != -1) {
    cashFlowTable.on('search.dt', function () {
        const totalValue = $("#cashFlowReport").find("[total-value]")
        const dataFilter = cashFlowTable.rows({ search: 'applied' }).data();

        let balance = 0
        dataFilter.each(function (row) {
            let entry = row.filter((value) => value.match(/^R\$\s[-\d,\.]+$/))
            entry = entry[0] ? entry[0] : 'R$ 0,00'
            let entryValue = parseFloat(entry.replace('R$', '').replace(/\./g, '').replace(',', '.').trim())

            balance += entryValue
        })

        balance < 0 ?
            totalValue.closest("tr").css("color", "#ff0000")
            : balance == 0 ? totalValue.closest("tr").removeAttr("style")
                : totalValue.closest("tr").css("color", "#008000")
        totalValue.html(balance.toLocaleString("pt-br", { "currency": "BRL", "style": "currency" }))
    })
}