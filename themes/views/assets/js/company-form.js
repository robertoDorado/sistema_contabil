if (window.location.pathname == '/admin/company/register') {
    $(document).ready(function () {
        $("[name='openingDate']").datepicker({
            format: "dd/mm/yyyy",
            language: "pt-BR",
            autoclose: true
        });
    });

    const mask = {
        cnpj: function(value) {
            return value.replace(/\D/g, "")
            .replace(/(\d{2})(\d)/, "$1.$2")
            .replace(/(\d{3})(\d)/, "$1.$2")
            .replace(/(\d{3})(\d)/, "$1/$2")
            .replace(/(\d{4})(\d)/, "$1-$2")
            .replace(/(-\d{2})\d+?$/, "$1")
        },

        cep: function(value) {
            return value.replace(/\D/g, "")
            .replace(/(\d{5})(\d)/, "$1-$2")
            .replace(/(-\d{3})\d+?$/, "$1")
        },

        number: function(value) {
            return value.replace(/\D/g, "")
        },

        phone: function(value) {
            return value.replace(/\D/g, "")
            .replace(/(\d{2})(\d)/, "($1) $2")
            .replace(/(\d{4})(\d)/, "$1-$2")
            .replace(/(-\d{4})\d+?$/, "$1")
        },

        cellPhone: function(value) {
            return value.replace(/\D/g, "")
            .replace(/(\d{2})(\d)/, "($1) $2")
            .replace(/(\d{5})(\d)/, "$1-$2")
            .replace(/(-\d{4})\d+?$/, "$1")
        },

        date: function(value) {
            return value.replace(/\D/g, "")
            .replace(/(\d{2})(\d)/, "$1/$2")
            .replace(/(\d{2})(\d)/, "$1/$2")
            .replace(/(\/\d{4})\d+?$/, "$1")
        },

        state: function(value) {
            return value.replace(/[^A-Za-z]+/g, '')
            .toUpperCase().replace(/([A-Z]{2})[A-Z]+?$/, "$1")
        },

        inscricaoEstadual: function(value) {
            return value.replace(/\D/g, "")
            .replace(/(\d{3})(\d)/, "$1.$2")
            .replace(/(\d{3})(\d)/, "$1.$2")
            .replace(/(\d{3})(\d)/, "$1.$2")
            .replace(/(\.\d{3})\d+?$/, "$1")
        }
    }

    const inputFields = Array.from(document.getElementsByTagName("input"))
    inputFields.forEach(function(element) {
        element.addEventListener("input", function() {
            if (this.dataset.mask) {
                this.value = mask[this.dataset.mask](this.value)
            }
        })
    })

    const companyForm = document.getElementById("companyForm")
    companyForm.addEventListener("submit", function(event) {
        event.preventDefault()
    })
}