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
            if (response.error) {
                let message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                modal.style.display = "none"
                throw new Error(message)
            }

            if (response.success) {
                window.location.href = window.location.href
            }
        })
    })

    window.addEventListener("load", function() {
        modal.style.display = "none";
    })
});