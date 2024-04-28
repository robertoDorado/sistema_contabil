if (window.location.pathname == "/admin/customer/cancel-subscription") {
    const cancelSubscription = document.querySelector("[cancelSubscription]")
    const saveChanges = document.getElementById("saveChanges")
    
    const dismissModal = document.getElementById("dismissModal")
    let cancelBtnTitle = "Cancelar a minha assinatura"

    cancelSubscription.addEventListener("click", function() {
        const launchModal = document.getElementById("launchModal")
        launchModal.click()
        
        const modalContainerLabel = document.getElementById("modalContainerLabel")
        const modalBody = document.querySelector(".modal-body")

        saveChanges.classList.remove("btn-primary")
        saveChanges.classList.add("btn-danger")

        modalContainerLabel.innerHTML = "Cancelamento da assinatura"
        modalBody.innerHTML = "Deseja mesmo cancelar a sua assinatura permanentemente?"
        dismissModal.innerHTML = "Não cancelar"
        saveChanges.innerHTML = cancelBtnTitle
    })

    saveChanges.addEventListener("click", function() {
        const btnCancelSubscription = this
        showSpinner(btnCancelSubscription)

        const form = new  FormData()
        form.append("cancelData", true)

        fetch(window.location.origin + "/customer/subscription/cancel-subscription", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            let message = ""

            if (response.error) {
                dismissModal.click()
                btnCancelSubscription.removeAttribute("disabled")
                btnCancelSubscription.innerHTML = cancelBtnTitle
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }
            
            if (response.success) {
                dismissModal.click()
                btnCancelSubscription.removeAttribute("disabled")
                btnCancelSubscription.innerHTML = cancelBtnTitle
                message = response.success
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.success(message)
                setTimeout(() => {
                    window.location.href = response.url
                }, 3000)
            }
        })
    })
}