if (window.location.pathname == "/admin/cash-flow-explanatory-notes/report") {
    cashFlowExplanatoryNotesReport.on("search.dt", function() {
        const totalValue = $("#cashFlowExplanatoryNotesReport").find("[total-value]")
        const dataFilter = cashFlowExplanatoryNotesReport.rows({ search: 'applied' }).data();
        let balance = 0

        dataFilter.each(function (row) {
            let entry = row.filter((value) => value.match(/^R\$\s[-\d,\.]+$/))
            entry = entry[0] ? entry[0] : 'R$ 0,00'
            let entryValue = parseFloat(entry.replace("R$", "").replace(".", "").replace(",", ".").trim())
            balance += entryValue
        })
        
        totalValue.html(balance.toLocaleString("pt-br", { "currency": "BRL", "style": "currency" }))
    })
}