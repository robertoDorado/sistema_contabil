if (window.location.pathname == "/admin/history-audit/backup") {
    const dataTransfer = {}
    const saveChanges = $("#saveChanges")

    historyAuditBackup.on("click", ".restore-link,.trash-link", function(event) {
        event.preventDefault()
        dataTransfer.row = $(this).closest("td").closest("tr")
        dataTransfer.uuid = $(this).data("uuid")
        dataTransfer.action = Array.isArray($(this).attr("class").match(/restore-link/)) ? "restore" : "delete"
        $("#launchModal").click()
    })

    $("#launchModal").click(function() {
        $("#modalContainerLabel").html("Atenção!")
        $("#dismissModal").html("Voltar")

        if (dataTransfer.action == "restore") {
            $(".modal-body").html(`Você deseja mesmo restaurar o registro ${dataTransfer.uuid}?`)
            saveChanges.removeClass("btn-danger")
            saveChanges.addClass("btn-primary")
            saveChanges.html("Restaurar")
        }else {
            $(".modal-body").html(`Você deseja mesmo deletar permanentemente o registro ${dataTransfer.uuid}?`)
            saveChanges.removeClass("btn-primary")
            saveChanges.addClass("btn-danger")
            saveChanges.html("Excluir")
        }
    })

    saveChanges.click(function() {
        showSpinner(this)

        const form = new FormData()
        form.append("uuid", dataTransfer.uuid)
        form.append("action", dataTransfer.action)
        
        const url = window.location.origin + "/admin/history-audit/backup"
        fetch(url, {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            saveChanges.removeAttr("disabled")
            let btnLabel = dataTransfer.action == "restore" ? "Restaurar" : "Excluir"
            saveChanges.html(btnLabel)
            let message = ""
            $("#dismissModal").click()

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
                historyAuditBackup.row(dataTransfer.row).remove().draw()
            }
        })
    })
}