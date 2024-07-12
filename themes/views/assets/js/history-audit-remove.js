if (window.location.pathname == "/admin/history-audit/report") {
    const dataTransfer = {
        uuid: "",
        row: null
    }

    historyAuditReport.on("click", ".trash-link", function(event) {
        event.preventDefault()
        dataTransfer.uuid = $(this).data("uuid")
        dataTransfer.row = $(this).closest("tr").prev().is("tr") ? $(this).closest("tr").prev() : $(this).closest("tr")
        $("#launchModal").click()
    })

    const saveChanges = $("#saveChanges")
    $("#launchModal").click(function() {
        $("#modalContainerLabel").html("Atenção!")
        $(".modal-body").html(`Você quer mesmo deletar o registro ${dataTransfer.uuid}?`)
        saveChanges.removeClass("btn-primary")
        saveChanges.addClass("btn-danger")
        saveChanges.html("Excluir")
        $("#dismissModal").html("Sair")
    })

    saveChanges.click(function() {
        const form = new FormData()
        form.append("uuid", dataTransfer.uuid)
        
        showSpinner(this)
        const url = window.location.origin + "/admin/history-audit/remove"
        fetch(url, {
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
                message = response.success
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.success(message)
                historyAuditReport.row(dataTransfer.row).remove().draw()
                $("#dismissModal").click()
            }
        })
    })
}