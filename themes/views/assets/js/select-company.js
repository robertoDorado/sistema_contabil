$(document).ready(function () {
    $('[name="selectCompanySession"]').select2();
    $('[name="selectCompanySession"]').on('select2:select', function (event) {
        const form = new FormData()
        form.append("companyId", event.params.data.id)
        
        modal.style.display = "flex";
        fetch(window.location.origin + "/admin/company/sesssion", {
            method: "POST",
            body: form
        }).then(data => data.json()).then(function (response) {
            if (!response.success) {
                toastr.error("Erro interno ao selecionar a empresa")
                throw new Error("Erro interno ao selecionar a empresa")
            }

            window.location.href = window.location.href
        })
    })

    window.addEventListener("load", function() {
        modal.style.display = "none";
    })
});