if (window.location.pathname == "/admin/balance-sheet/chart-of-account-group/backup") {
    const dataTransfer = {}
    const launchModal = $("#launchModal")
    const saveChanges = $("#saveChanges")
    
    chartOfAccountGroupBackup.on("click", ".trash-link,.restore-link", function(event) {
        event.preventDefault()
        dataTransfer.uuid = $(this).data("uuid")
        dataTransfer.accountName = $(this).data("accountname")
        dataTransfer.row = $(this).closest("tr")
        dataTransfer.action = Array.isArray($(this).attr("class").match(/restore-link/)) ? "restore" : "delete"
        launchModal.click()
    })

    launchModal.click(function() {
        $("#modalContainerLabel").html("Atenção!")
        $("#dismissModal").html("Voltar")
        if (dataTransfer.action == "restore") {
            $(".modal-body").html(`Você deseja mesmo restaurar a conta '${dataTransfer.accountName}'?`)
            saveChanges.removeClass("btn-danger")
            saveChanges.addClass("btn-primary")
            saveChanges.html("Restaurar")
        }else {
            $(".modal-body").html(`Você deseja mesmo excluir a conta '${dataTransfer.accountName}'?`)
            saveChanges.removeClass("btn-primary")
            saveChanges.addClass("btn-danger")
            saveChanges.html("Excluir")
        }
    })

    saveChanges.click(function() {
        const btnSubmit = this
        showSpinner(btnSubmit)
        
        const form = new FormData()
        form.append("uuid", dataTransfer.uuid)
        form.append("action", dataTransfer.action)

        fetch(window.location.origin + "/admin/balance-sheet/chart-of-account-group/backup", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = dataTransfer.action == "restore" ? "Restaurar" : "Excluir"
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
                chartOfAccountGroupBackup.row(dataTransfer.row).remove().draw(false)
                $("#dismissModal").click()
            }
        })
    })
}