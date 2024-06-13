let currentEndpoint = window.location.pathname
let uuid = ""

if (/\/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/.test(currentEndpoint)) {
    currentEndpoint = currentEndpoint.split("/")
    uuid = currentEndpoint.pop()
    currentEndpoint = currentEndpoint.join("/")
}

const endpoints = ["/admin/company/register", "/admin/company/update/form"]
if (endpoints.indexOf(currentEndpoint) != -1) {
    $(document).ready(function () {
        $("[name='openingDate']").datepicker({
            format: "dd/mm/yyyy",
            language: "pt-BR",
            autoclose: true
        });
    });

    const mask = {
        cnpj: function (value) {
            return value.replace(/\D/g, "")
                .replace(/(\d{2})(\d)/, "$1.$2")
                .replace(/(\d{3})(\d)/, "$1.$2")
                .replace(/(\d{3})(\d)/, "$1/$2")
                .replace(/(\d{4})(\d)/, "$1-$2")
                .replace(/(-\d{2})\d+?$/, "$1")
        },

        cep: function (value) {
            return value.replace(/\D/g, "")
                .replace(/(\d{5})(\d)/, "$1-$2")
                .replace(/(-\d{3})\d+?$/, "$1")
        },

        number: function (value) {
            return value.replace(/\D/g, "")
        },

        phone: function (value) {
            return value.replace(/\D/g, "")
                .replace(/(\d{2})(\d)/, "($1) $2")
                .replace(/(\d{4})(\d)/, "$1-$2")
                .replace(/(-\d{4})\d+?$/, "$1")
        },

        cellPhone: function (value) {
            return value.replace(/\D/g, "")
                .replace(/(\d{2})(\d)/, "($1) $2")
                .replace(/(\d{5})(\d)/, "$1-$2")
                .replace(/(-\d{4})\d+?$/, "$1")
        },

        date: function (value) {
            return value.replace(/\D/g, "")
                .replace(/(\d{2})(\d)/, "$1/$2")
                .replace(/(\d{2})(\d)/, "$1/$2")
                .replace(/(\/\d{4})\d+?$/, "$1")
        },

        state: function (value) {
            return value.replace(/[^A-Za-z]+/g, '')
                .toUpperCase().replace(/([A-Z]{2})[A-Z]+?$/, "$1")
        },

        inscricaoEstadual: function (value) {
            return value.replace(/\D/g, "")
                .replace(/(\d{3})(\d)/, "$1.$2")
                .replace(/(\d{3})(\d)/, "$1.$2")
                .replace(/(\d{3})(\d)/, "$1.$2")
                .replace(/(\.\d{3})\d+?$/, "$1")
        }
    }

    const inputFields = Array.from(document.getElementsByTagName("input"))
    inputFields.forEach(function (element) {
        element.addEventListener("input", function () {
            if (this.dataset.mask) {
                this.value = mask[this.dataset.mask](this.value)
            }
        })
    })

    const companyZipcode = document.querySelector("[name='companyZipcode']")
    companyZipcode.addEventListener("input", function () {
        const searchField = this.value.replace(/\D/g, "").replace(/(\d{5})(\d)/, "$1$2")
        if (searchField.length >= 8) {
            fetch(`https://brasilapi.com.br/api/cep/v1/${searchField}`)
                .then(data => data.json()).then(function (response) {
                    if (response.cep) {
                        document.querySelector("[name='companyAddress']").value = response.street
                        document.querySelector("[name='companyNeighborhood']").value = response.neighborhood
                        document.querySelector("[name='companyCity']").value = response.city
                        document.querySelector("[name='companyState']").value = response.state
                    }
                })
        }
    })

    const companyForm = document.getElementById("companyForm")
    companyForm.addEventListener("submit", function (event) {
        event.preventDefault()

        const btnSubmit = this.querySelector("[type='submit']")
        const allowFields = ["stateRegistration", "webSite", "companyEmail", "companyPhone"]

        inputFields.forEach(function (element) {
            if (allowFields.indexOf(element.name) == -1) {
                if (!element.value) {
                    toastr.error(`Campo ${element.previousElementSibling.innerHTML.toLowerCase()} é obrigatório`)
                    throw new Error(`campo ${element.name} é obrigatório`)
                }
            }
        })

        const form = new FormData(this)
        if (uuid) {
            form.append("uuid", uuid)
        }

        showSpinner(btnSubmit)
        fetch(currentEndpoint, {
            method: "POST",
            body: form
        }).then(data => data.json()).then(function (response) {
            btnSubmit.innerHTML = "Enviar"
            btnSubmit.removeAttribute("disabled")
            
            if (response.error) {
                let message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.success) {
                if (!uuid) {
                    toastr.success("Empresa criada com sucesso")
                    inputFields.forEach(function (element) {
                        if (element.name != "csrfToken") {
                            element.value = ""
                        }
                    })
                } else {
                    window.location.href = window.location.origin + "/admin/company/report"
                }
            }
        })
    })
}