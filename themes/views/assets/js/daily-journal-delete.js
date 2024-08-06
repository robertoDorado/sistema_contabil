if  (window.location.pathname == "/admin/balance-sheet/daily-journal/report") {
    const dataTransfer = {}
    const launchModal = $("#launchModal")
    const saveChanges = $("#saveChanges")
    const dismissModal = $("#dismissModal")

    dailyJournalReport.on("click", "#trashLink", function(event) {
        event.preventDefault()
        dataTransfer.uuid = $(this).data("uuid")
        dataTransfer.account_name = $(this).data("accountname")
        dataTransfer.row = $(this).closest("tr")
        launchModal.click()
    })

    launchModal.click(function() {
        $("#modalContainerLabel").html("Atenção!")
        $(".modal-body").html(`Deseja mesmo remover o registro ${dataTransfer.account_name}?`)
        dismissModal.html("Voltar")
        saveChanges.removeClass("btn-primary")
        saveChanges.addClass("btn-danger")
        saveChanges.html("Excluir")
    })

    saveChanges.click(function() {
        showSpinner(this)

        const form = new FormData()
        form.append("uuid", dataTransfer.uuid)
        fetch(window.location.origin + "/admin/balance-sheet/daily-journal/delete", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            let message = ""
            saveChanges.removeAttr("disabled")
            saveChanges.html("Excluir")

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
                dailyJournalReport.row(dataTransfer.row).remove().draw()
                dismissModal.click()
            }
        })
    })
}