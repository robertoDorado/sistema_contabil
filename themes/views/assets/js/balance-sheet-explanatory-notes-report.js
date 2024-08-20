if (window.location.pathname == "/admin/balance-sheet-explanatory-notes/report") {
    const dataTransfer = {}
    const launchModal = $("#launchModal")
    const saveChanges = $("#saveChanges")
    const dismissModal = $("#dismissModal")

    balanceSheetExplanatoryNotesReport.on("click", "[trash-icon]", function(event) {
        event.preventDefault()
        dataTransfer.row = $(this).closest("tr")
        dataTransfer.uuid = $(this).data("uuid")
        dataTransfer.account_name = $(this).data("accountname")
        launchModal.click()
    })

    launchModal.click(function() {
        $("modalContainerLabel").html("Atenção!")
        $(".modal-body").html(`Deseja mesmo deletar a conta ${dataTransfer.account_name}?`)
        $("#dismissModal").html("Voltar")
        saveChanges.html("Excluir")
        saveChanges.removeClass("btn-primary")
        saveChanges.addClass("btn-danger")
    })

    saveChanges.click(function() {
        showSpinner(this)

        const form = new FormData()
        form.append("uuid", dataTransfer.uuid)

        fetch(window.location.origin + "/admin/balance-sheet-explanatory-notes/form/remove", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            saveChanges.removeAttr("disabled")
            saveChanges.html("Excluir")
            let message = ""

            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.success) {
                dismissModal.click()
                message = response.success
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.success(message)
                balanceSheetExplanatoryNotesReport.row(dataTransfer.row).remove().draw(false)
            }
        })
    })
}