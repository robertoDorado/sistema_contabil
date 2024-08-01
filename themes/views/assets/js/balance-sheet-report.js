if (window.location.pathname == "/admin/balance-sheet/balance-sheet-overview/report") {
    const closeAccountingPeriod = document.getElementById("closeAccountingPeriod")
    closeAccountingPeriod.addEventListener("click", function () {
        const btnText = this.innerHTML
        showSpinner(this)

        const queryString = new URLSearchParams(window.location.search)
        const form = new FormData()

        form.append("closeAccounting", true)
        form.append("date", queryString.get("daterange"))

        fetch(window.location.origin + "/admin/balance-sheet/balance-sheet-overview/report", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function (response) {
            closeAccountingPeriod.removeAttribute("disabled")
            closeAccountingPeriod.innerHTML = btnText
            let message = ""

            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.success) {
                message = response.success
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.success(message)
                shareholdersEquity.row.add([
                    response.profit_accounting.uuid,
                    response.profit_accounting.created_at,
                    response.profit_accounting.account_number  + " " + response.profit_accounting.account_name,
                    response.profit_accounting.account_value_formated
                ]).draw(false)

                let totalShareholdersEquityAndLiabilities = document.getElementById("totalShareholdersEquityAndLiabilities").innerHTML
                totalShareholdersEquityAndLiabilities = parseFloat(totalShareholdersEquityAndLiabilities
                .replace(/\./g, "").replace(/[R\$\s]+/, "").replace(",", "."))

                totalShareholdersEquityAndLiabilities += response.profit_accounting.account_value
                document.getElementById("totalShareholdersEquityAndLiabilities").innerHTML = totalShareholdersEquityAndLiabilities
                .toLocaleString("pt-br", { "currency": "BRL", "style": "currency" })
            }
        })
    })
}