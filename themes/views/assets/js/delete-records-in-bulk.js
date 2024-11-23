if (window.location.pathname == "/admin/cash-flow/report") {
    $("#deleteRecordsInBulk").click(function() {
        const btnDelete = this
        const btnDeleteText = this.innerHTML

        showSpinner(btnDelete)
        const queryStringParams = new URLSearchParams(window.location.search)
        const form = new FormData()
        form.append("daterange", queryStringParams.get("daterange"))
        
        fetch("/admin/cash-flow/delete-in-bulk", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            btnDelete.removeAttribute('disabled')
            btnDelete.innerHTML = btnDeleteText
            let message = ''

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
                cashFlowTable.rows().remove().draw()
            }
        })
    })
}