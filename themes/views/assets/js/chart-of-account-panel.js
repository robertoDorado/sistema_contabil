if (window.location.pathname == "/admin/balance-sheet/chart-of-account") {
    const exportExcelModelChartOfAccount = document.getElementById("exportExcelModelChartOfAccount")
    exportExcelModelChartOfAccount.addEventListener("click", function () {
        const btnExport = this
        showSpinner(btnExport)

        fetch(window.location.origin + "/admin/balance-sheet/export-model-chart-of-account", {
            method: "POST"
        }).then(response => response.blob()).then(function (response) {
            const url = window.URL.createObjectURL(response)
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = 'modelo-plano-de-contas.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            btnExport.removeAttribute("disabled")
            btnExport.innerHTML = "Exportar modelo de plano de contas"
        })
    })

    const mask = {
        accountValue: function (value) {
            return value.replace(/[^\d\.]+/g, '').replace(/\.(\.)/, '$1')
        }
    }

    const accountValue = document.querySelector("[name='accountValue']")
    accountValue.addEventListener("input", function() {
        this.value = mask[this.dataset.mask](this.value)
    })

    const cashFlowGroupForm = document.getElementById("cashFlowGroupForm")
    cashFlowGroupForm.addEventListener("submit", function(event) {
        event.preventDefault()
        const btnSubmit = this.querySelector("button[type='submit']")

        if (Array.isArray(this.accountValue.value.match(/\d+\.$/))) {
            toastr.error("Número de conta inválido")
            throw new Error("Número de conta inválido")
        }

        const form = new FormData(this)
        const url = window.location.origin + "/admin/balance-sheet/chart-of-account"
        showSpinner(btnSubmit)

        fetch(url, {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Enviar"
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
                chartOfAccount.row.add([
                    response.data.uuid,
                    response.data.accountValue,
                    response.data.accountName,
                    response.data.editBtn,
                    response.data.excludeBtn
                ]).draw(false)
            }
        })
    })
}