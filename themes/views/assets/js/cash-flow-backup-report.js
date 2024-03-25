if (window.location.pathname == "/admin/cash-flow/backup/report") {
    const cashFlowDeletedTableReport = document.getElementById("cashFlowDeletedReport")
    const launchModal = document.getElementById("launchModal")
    const modalContainer = document.getElementById("modalContainer")
    const saveChanges = modalContainer.querySelector("#saveChanges")
    const dismissModal = modalContainer.querySelector("#dismissModal")
    
    const tBody = Array.from(cashFlowDeletedTableReport.querySelector("tBody").children)
    const data = {
        restore: false,
        destroy: false
    }
    
    tBody.forEach(function(row) {
        const btnRestoreData = row.lastElementChild.previousElementSibling.firstElementChild
        const btnDestroyData = row.lastElementChild.firstElementChild

        btnRestoreData.addEventListener("click", function(event) {
            event.preventDefault()
            const uuid = this.parentElement
            .previousElementSibling
            .previousElementSibling
            .previousElementSibling
            .previousElementSibling
            .previousElementSibling
            .previousElementSibling.innerHTML
            const row = this.parentElement.parentElement
            data.row = row
            data.uuid = uuid
            data.restore = true
            data.destroy = false
            launchModal.click()
        })

        btnDestroyData.addEventListener("click", function(event) {
            event.preventDefault()
            const uuid = this.parentElement
            .previousElementSibling
            .previousElementSibling
            .previousElementSibling
            .previousElementSibling
            .previousElementSibling
            .previousElementSibling
            .previousElementSibling.innerHTML
            const row = this.parentElement.parentElement
            data.row = row
            data.uuid = uuid
            data.destroy = true
            data.restore = false
            launchModal.click()
        })
    })

    launchModal.addEventListener("click", function() {
        if (data.restore) {
            saveChanges.innerHTML = "Restaurar"
            saveChanges.classList.remove("btn-danger")
            saveChanges.classList.add("btn-primary")
            dismissModal.innerHTML = "Voltar";
            modalContainer.querySelector("#modalContainerLabel").innerHTML = "Restaurar registro"
            modalContainer.querySelector(".modal-body").innerHTML = `Deseja mesmo restaurar o registro ${data.uuid}?`
        }

        if (data.destroy) {
            saveChanges.innerHTML = "Excluir"
            saveChanges.classList.remove("btn-primary")
            saveChanges.classList.add("btn-danger")
            dismissModal.innerHTML = "Voltar";
            modalContainer.querySelector("#modalContainerLabel").innerHTML = "Excluir registro"
            modalContainer.querySelector(".modal-body")
            .innerHTML = `Deseja mesmo excluir permanentemente o registro ${data.uuid}?`
        }
    })

    saveChanges.addEventListener("click", function() {
        const saveChanges = this
        showSpinner(saveChanges)
        
        const form = new FormData()
        form.append("destroy", data.destroy)
        form.append("restore", data.restore)

        fetch(window.location.origin + `/admin/cash-flow/modify/${data.uuid}`,
        {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            let message = ""
            saveChanges.innerHTML = "Restaurar"

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
                cashFlowDeletedReport.row(data.row).remove().draw()
                dismissModal.click()
            }
        })
    })
}