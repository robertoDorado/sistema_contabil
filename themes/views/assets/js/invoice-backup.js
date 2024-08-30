if (window.location.pathname == "/admin/invoice/backup") {
    const dataTransfer = {}
    const launchModal = $("#launchModal")
    const dismissModal = $("#dismissModal")
    const saveChanges = $("#saveChanges")

    invoiceReportBackup.on("click", "#restoreInvoice,#deleteInvoice", function(event) {
        event.preventDefault()
        dataTransfer.uuid = $(this).data("uuid")
        dataTransfer.id = $(this).data("id")
        dataTransfer.action = Array.isArray($(this).attr("id").match(/restoreInvoice/)) ? "restore" : "delete"
        launchModal.click()
    })

    const verifyAction = {
        "restore": function() {
            $(".modal-body").html(`Deseja mesmo restaurar o registro ${dataTransfer.id}?`)
            saveChanges.html("Restaurar")
            saveChanges.removeClass("btn-danger")
            saveChanges.addClass("btn-primary")
        },
        "delete": function() {
            $(".modal-body").html(`Deseja mesmo excluir permanentemente o registro ${dataTransfer.id}?`)
            saveChanges.html("Excluir")
            saveChanges.removeClass("btn-primary")
            saveChanges.addClass("btn-danger")
        }
    }

    launchModal.click(function() {
        $("#modalContainerLabel").html("Atenção!")
        dismissModal.html("Voltar")

        if (typeof verifyAction[dataTransfer.action] == "function") {
            verifyAction[dataTransfer.action]()
        }
    })

    saveChanges.click(function() {
        showSpinner(this)
        const form = new FormData()
        form.append("uuid", dataTransfer.uuid)
        form.append("action", dataTransfer.action)

        fetch(window.location.origin + "/admin/invoice/backup", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            saveChanges.removeAttr("disabled")
            const saveChangesLabel = dataTransfer.action == "restore" ? "Restaurar" : "Excluir"
            saveChanges.html(saveChangesLabel)
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
                invoiceReportBackup.row(dataTransfer.row).remove().draw(false)
            }
        })
    })
}