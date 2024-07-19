if (window.location.pathname == "/admin/cash-flow-explanatory-notes/backup") {
    const dataTransfer = {
        uuid: "",
        row: null,
        accountName: "",
        action: ""
    }

    const launcModal = $("#launchModal")
    cashFlowExplanatoryNotesBackup.on("click", "[database-icon],[trash-icon]", function(event) {
        event.preventDefault()
        dataTransfer.uuid = $(this).data("uuid")
        dataTransfer.accountName = $(this).data("accountname")
        dataTransfer.row = $(this).closest("tr")
        dataTransfer.action = Array.isArray(this.classList.value.match(/restore-icon/)) ? "restore" : "delete"
        launcModal.click()
    })

    launcModal.click(function() {
        $("#modalContainerLabel").html("Atenção!")
        $("#dismissModal").html("Voltar")
        $("#saveChanges").removeClass("btn-primary")
        $("#saveChanges").removeClass("btn-danger")
        
        if (dataTransfer.action == "restore") {
            $(".modal-body").html(`Você deseja restaurar a conta "${dataTransfer.accountName}"?`)
            $("#saveChanges").addClass("btn-primary")
            $("#saveChanges").html("Restaurar")
        }else {
            $(".modal-body").html(`Você deseja excluir permanentemente a conta "${dataTransfer.accountName}"?`)
            $("#saveChanges").addClass("btn-danger")
            $("#saveChanges").html("Excluir")
        }
    })

    $("#saveChanges").click(function() {
        const saveChanges = this
        showSpinner(saveChanges)
        
        const form = new FormData()
        form.append("csrfToken", dataTransfer.csrf)
        form.append("action", dataTransfer.action)
        form.append("uuid", dataTransfer.uuid)

        const url = window.location.origin + window.location.pathname
        fetch(url, {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            saveChanges.removeAttribute("disabled")
            saveChanges.innerHTML = dataTransfer.action == "restore" ? "Restaurar" : "Excluir"
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
                cashFlowExplanatoryNotesBackup.row(dataTransfer.row).remove().draw(false)
                $("#dismissModal").click()
            }
        })
    })
}