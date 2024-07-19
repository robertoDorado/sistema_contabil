if (window.location.pathname == '/admin/company/report') {
    $(document).ready(function () {
        let deleteData = {}
        $("#companyReport").on("click", "a.trash-link", function (event) {
            event.preventDefault()
            $("#launchModal").click()
            $("#modalContainerLabel").html("Atenção!")
            $("#dismissModal").html("Voltar")
            $("#saveChanges").removeClass("btn-primary").addClass("btn-danger").html("Excluir")
            $(".modal-body").html(`Você quer mesmo deletar o registro "${$(this).data("company")}"?`)
            deleteData.uuid  = $(this).data("uuid")
            deleteData.trElement = $(this).closest("tr").prev()
            deleteData.csrfToken = $(this).data("csrf")
        })

        $("#saveChanges").click(function () {
            const form = new FormData()
            form.append("uuid", deleteData.uuid)
            form.append("csrfToken", deleteData.csrfToken)
            
            btnSaveChanges = this
            showSpinner(btnSaveChanges)
            
            fetch(window.location.origin + "/admin/company/delete", {
                method: "POST",
                body: form
            }).then(data => data.json()).then(function(response) {
                btnSaveChanges.removeAttribute("disabled")
                btnSaveChanges.innerHTML = "Excluir"

                if (response.error) {
                    let message = response.error
                    message = message.charAt(0).toUpperCase() + message.slice(1)
                    toastr.error(message)
                    $("#dismissModal").click()
                    throw new Error(message)
                }

                if (response.success) {
                    companyReport.row(deleteData.trElement).remove().draw(false)
                    $("#dismissModal").click()
                }
            })
        })
    })
}