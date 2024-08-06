if (window.location.pathname == "/admin/balance-sheet/daily-journal/report/backup") {
    const dataTransfer = {}
    const launchModal = $("#launchModal")
    const saveChanges = $("#saveChanges")
    const dismissModal = $("#dismissModal")
    
    dailyJournalBackup.on("click", "#restoreLink,#trashLink", function(event) {
        event.preventDefault()
        dataTransfer.uuid = $(this).data("uuid")
        dataTransfer.account_name = $(this).data("accountname")
        dataTransfer.row = $(this).closest("tr")
        dataTransfer.action = Array.isArray($(this).attr("id").match(/^restoreLink$/)) ? "restore" : "delete"
        launchModal.click()
    })

    
    launchModal.click(function() {
        const verifyActionByDataTransfer = {
            "restore": function() {
                $(".modal-body").html(`Desja mesmo restaurar o registro ${dataTransfer.account_name}?`)
                saveChanges.removeClass("btn-danger")
                saveChanges.addClass("btn-primary")
                saveChanges.html("Restaurar")
            },
            
            "delete": function() {
                $(".modal-body").html(`Desja mesmo deletar permanentemente o registro ${dataTransfer.account_name}?`)
                saveChanges.removeClass("btn-primary")
                saveChanges.addClass("btn-danger")
                saveChanges.html("Excluir")
            }
        }

        $("#modalContainerLabel").html("Atenção!")
        dismissModal.html("Voltar")
        if (typeof verifyActionByDataTransfer[dataTransfer.action] == "function") {
            verifyActionByDataTransfer[dataTransfer.action]()
        }
    })

    saveChanges.click(function() {
        showSpinner(this)

        const form = new FormData()
        form.append("uuid", dataTransfer.uuid)
        form.append("action", dataTransfer.action)

        fetch(window.location.origin + "/admin/balance-sheet/daily-journal/report/backup", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            saveChanges.removeAttr("disabled")
            saveChanges.html(dataTransfer.action == "restore" ? "Restaurar" : "Excluir")
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
                dailyJournalBackup.row(dataTransfer.row).remove().draw(false)
                dismissModal.click()
            }
        })
    })
}