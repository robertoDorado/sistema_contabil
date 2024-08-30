if (window.location.pathname == '/admin/invoice/report') {
    invoiceReport.on("click", "#danfeEmission", function(event) {
        event.preventDefault()
        const btnEmission = this
        const uuid = this.dataset.uuid
    
        const form = new FormData()
        form.append("uuid", uuid)
    
        showSpinner(btnEmission)
        fetch(window.location.origin + "/admin/invoice/emission/danfe", {
            method: "POST",
            body: form
        }).then(response => response.blob()).then(function(response) {
            downloadRequestPost(response, `danfe-${uuid}.pdf`)
            btnEmission.removeAttribute("disabled")
            btnEmission.innerHTML = "Emissão da Danfe"
        })
    })

    const dataTransfer = {}
    const launchModal = $("#launchModal")
    const dismissModal = $("#dismissModal")
    const saveChanges = $("#saveChanges")

    invoiceReport.on("click", "#deleteInvoice", function(event) {
        event.preventDefault()
        dataTransfer.uuid = $(this).data("uuid")
        dataTransfer.row = $(this).closest("tr")
        dataTransfer.id = $(this).data("id")
        launchModal.click()
    })

    launchModal.click(function() {
        $("#modalContainerLabel").html("Atenção!")
        $(".modal-body").html(`Deseja mesmo excluir o registro ${dataTransfer.id}?`)
        dismissModal.html("Voltar")
        saveChanges.html("Excluir")
        saveChanges.removeClass("btn-primary")
        saveChanges.addClass("btn-danger")
    })

    saveChanges.click(function() {
        showSpinner(this)
        const form = new FormData()

        form.append("uuid", dataTransfer.uuid)
        fetch(window.location.origin + "/admin/invoice/remove", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            saveChanges.removeAttr("disabled")
            saveChanges.html("Excluir")
            dismissModal.click()
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
                invoiceReport.row(dataTransfer.row).remove().draw(false)
            }
        })
    })
}