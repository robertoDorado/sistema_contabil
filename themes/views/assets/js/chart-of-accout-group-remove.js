if (window.location.pathname == "/admin/balance-sheet/chart-of-account-group") {
    const dataTransfer = {}
    const launchModal = $("#launchModal")
    const saveChanges = $("#saveChanges")

    chartOfAccountGroup.on("click", ".trash-link", function (event) {
        event.preventDefault()
        dataTransfer.uuid = $(this).data("uuid")
        dataTransfer.accountName = $(this).data("accountname")
        dataTransfer.row = $(this).closest("tr")
        launchModal.click()
    })

    launchModal.click(function () {
        $("#modalContainerLabel").html("Atenção!")
        $(".modal-body").html(`Você deseja mesmo deletar a conta '${dataTransfer.accountName}'?`)
        saveChanges.removeClass("btn-primary")
        saveChanges.addClass("btn-danger")
        saveChanges.html("Excluir")
        $("#dismissModal").html("Voltar")
    })

    saveChanges.click(function () {
        const btnSaveChanges = this
        showSpinner(btnSaveChanges)

        const form = new FormData()
        form.append("uuid", dataTransfer.uuid)

        fetch(window.location.origin + "/admin/balance-sheet/chart-of-account-group/delete", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function (response) {
            btnSaveChanges.removeAttribute("disabled")
            btnSaveChanges.innerHTML = "Excluir"
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
                chartOfAccountGroup.row(dataTransfer.row).remove().draw(false)
                $("#dismissModal").click()
            }
        })
    })
}