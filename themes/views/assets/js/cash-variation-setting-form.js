const cashVariationEndpointFormUrl = window.location.pathname.replace(/(\/\w{8}-\w{4}-\w{4}-\w{4}-\w{12})/, "")
let cashVariationUuid = window.location.pathname.match(/(\w{8}-\w{4}-\w{4}-\w{4}-\w{12})/)
cashVariationUuid = cashVariationUuid && cashVariationUuid.length > 0 ? cashVariationUuid[0] : ""

const cashVariationAllowEndpoints = [
    "/admin/cash-variation-setting/form",
    "/admin/cash-variation-setting/form-update"
]

if (cashVariationAllowEndpoints.indexOf(cashVariationEndpointFormUrl) != -1) {
    const accountGroup = $("#accountGroup").select2()
    document.getElementById("cashVariationForm").addEventListener("submit", function (event) {
        event.preventDefault()
        const btnSubmit = this.querySelector("[type='submit']")

        let message = ""
        if (!this.accountGroupVariation.value) {
            message = "O campo grupo de variação está vazio ou inválido"
            toastr.warning(message)
            throw new Error(message)
        }

        if (!this.accountGroup.value) {
            message = "O campo grupo de contas está vazio ou inválido"
            toastr.warning(message)
            throw new Error(message)
        }

        if (!this.csrfToken.value) {
            message = "O campo token csrf está vazio ou inválido"
            toastr.warning(message)
            throw new Error(message)
        }

        const accountGroupVariation = this.accountGroupVariation
        const form = new FormData(this)
        if (cashVariationUuid) {
            form.append("currentUuid", cashVariationUuid)
        }

        showSpinner(btnSubmit)
        fetch(cashVariationEndpointFormUrl, {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function (response) {

            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Enviar"

            accountGroup.val(null).trigger("change")
            accountGroupVariation.value = ""

            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (cashVariationUuid) {
                modal.style.display = "flex"
            }

            if (response.success) {
                message = response.success
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.success(message)
            }

            if (response.redirect) {
                window.location.href = response.redirect
            }
        })
    })
}