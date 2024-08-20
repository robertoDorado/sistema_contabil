if (window.location.pathname == "/admin/balance-sheet-explanatory-notes/form/backup") {
    const dataTransfer = {}
    const launchModal = $("#launchModal")
    const saveChanges = $("#saveChanges")
    const dismissModal = $("#dismissModal")

    balanceSheetExplanatoryNotesBackup.on("click", "[trash-icon],[restore-icon]", function(event) {
        event.preventDefault()
        dataTransfer.uuid = $(this).data("uuid")
        dataTransfer.account_name = $(this).data("accountname")
        dataTransfer.row = $(this).closest("tr")
        dataTransfer.action = Array.isArray($(this).data("action").match(/restore/)) ? "restore" : "delete"
        launchModal.click()
    })

    const verifyAction = {
        "restore": function() {
            $(".modal-body").html(`Você deseja mesmo restaurar o registro ${dataTransfer.account_name}`)
            saveChanges.removeClass("btn-danger")
            saveChanges.addClass("btn-primary")
            saveChanges.html("Restaurar")
        },

        "delete": function() {
            $(".modal-body").html(`Você deseja mesmo deletar o registro ${dataTransfer.account_name}`)
            saveChanges.removeClass("btn-primary")
            saveChanges.addClass("btn-danger")
            saveChanges.html("Excluir")
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

        fetch(window.location.origin + "/admin/balance-sheet-explanatory-notes/form/backup", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            saveChanges.removeAttr("disabled")
            saveChanges.html("Atualizar")
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
                balanceSheetExplanatoryNotesBackup.row(dataTransfer.row).remove().draw(false)
                dismissModal.click()
            }
        })
    })
}