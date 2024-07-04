if (window.location.pathname == "/admin/company/backup/report") {
    const companyTableDeletedReport = document.getElementById("companyDeletedReport")
    const launchModal = document.getElementById("launchModal")
    const modalContainer = document.getElementById("modalContainer")
    const saveChanges = modalContainer.querySelector("#saveChanges")
    const dismissModal = modalContainer.querySelector("#dismissModal")
    
    const tBody = Array.from(companyTableDeletedReport.querySelector("tBody").children)
    const data = {
        restore: false,
        destroy: false
    }
    
    tBody.forEach(function(row) {
        const btnRestoreData = row.lastElementChild.previousElementSibling.firstElementChild
        const btnDestroyData = row.lastElementChild.firstElementChild

        btnRestoreData.addEventListener("click", function(event) {
            event.preventDefault()
            const uuid = this.dataset.uuid
            const row = this.closest("tr")
            data.row = row
            data.uuid = uuid
            data.restore = true
            data.destroy = false
            data.csrf = this.closest("td").dataset.csrf
            data.nameReference = Array.from(this.closest("tr").children)[0].innerHTML
            launchModal.click()
        })

        btnDestroyData.addEventListener("click", function(event) {
            event.preventDefault()
            const uuid = this.dataset.uuid
            const row = this.closest("tr")
            data.row = row
            data.uuid = uuid
            data.destroy = true
            data.restore = false
            data.csrf = this.closest("td").dataset.csrf
            data.nameReference = Array.from(this.closest("tr").children)[0].innerHTML
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
            modalContainer.querySelector(".modal-body").innerHTML = `Deseja mesmo restaurar o registro "${data.nameReference}"?`
        }

        if (data.destroy) {
            saveChanges.innerHTML = "Excluir"
            saveChanges.classList.remove("btn-primary")
            saveChanges.classList.add("btn-danger")
            dismissModal.innerHTML = "Voltar";
            modalContainer.querySelector("#modalContainerLabel").innerHTML = "Excluir registro"
            modalContainer.querySelector(".modal-body")
            .innerHTML = `Deseja mesmo excluir permanentemente o registro "${data.nameReference}"?`
        }
    })

    saveChanges.addEventListener("click", function() {
        const saveChanges = this
        showSpinner(saveChanges)
        
        const form = new FormData()
        form.append("uuid", data.uuid)
        form.append("destroy", data.destroy)
        form.append("restore", data.restore)
        form.append("csrfToken", data.csrf)

        fetch(window.location.origin + "/admin/company/modify",
        {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            let message = ""
            saveChanges.innerHTML = data.destroy ? "Excluir" : "Restaurar"
            saveChanges.removeAttribute("disabled")

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
                companyDeletedReport.row(data.row).remove().draw()
                dismissModal.click()
            }
        })
    })
}