const chartOfAccountPathName = window.location.pathname.replace(/\/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/, '')
const chartOfAccountUuid = Array.isArray(window.location.pathname.match(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/)) ?
    window.location.pathname.match(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/)[0] : ""

const verifyChartOfAccountPathname = [
    "/admin/balance-sheet/chart-of-account",
    "/admin/balance-sheet/chart-of-account/update"
]

if (verifyChartOfAccountPathname.indexOf(chartOfAccountPathName) != -1) {
    $("#chartOfAccountGroupSelect").select2()
    const exportExcelModelChartOfAccount = document.getElementById("exportExcelModelChartOfAccount")
    const dataTransfer = {}
    
    if (exportExcelModelChartOfAccount) {
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
    }

    const mask = {
        accountValue: function (value) {
            return value.replace(/[^\d\.]+/g, '').replace(/\.(\.)/, '$1')
        }
    }

    const accountValue = document.querySelector("[name='accountValue']")
    accountValue.addEventListener("input", function () {
        this.value = mask[this.dataset.mask](this.value)
    })

    const chartOfAccountForm = document.getElementById("chartOfAccountForm")
    chartOfAccountForm.addEventListener("submit", function (event) {
        event.preventDefault()
        const btnSubmit = this.querySelector("button[type='submit']")

        if (!this.accountValue.value) {
            toastr.warning("Número da conta é obrigatório")
            throw new Error("Número da conta é obrigatório")
        }

        if (!this.accountName.value) {
            toastr.warning("Nome da conta é obrigatório")
            throw new Error("Nome da conta é obrigatório")
        }

        if (!this.chartOfAccountGroupSelect.value) {
            toastr.warning("Grupo de contas é obrigatório")
            throw new Error("Grupo de contas é obrigatório")
        }

        if (!this.csrfToken.value) {
            toastr.warning("Token csrf é obrigatório")
            throw new Error("Token csrf é obrigatório")
        }
        
        if (Array.isArray(this.accountNumber.value.match(/^\.$/))) {
            toastr.error("Número da conta inválido")
            throw new Error("Número de conta inválido")
        }

        const form = new FormData(this)
        if (chartOfAccountPathName == "/admin/balance-sheet/chart-of-account/update") {
            form.append("uuid", chartOfAccountUuid)
        }

        const url = chartOfAccountPathName == "/admin/balance-sheet/chart-of-account" ?
            window.location.origin + "/admin/balance-sheet/chart-of-account" :
            window.location.origin + "/admin/balance-sheet/chart-of-account/update"

        const resetInputValues = [this.accountName, this.accountValue]
        showSpinner(btnSubmit)
        fetch(url, {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function (response) {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Enviar"
            let message = ""

            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (chartOfAccountPathName == "/admin/balance-sheet/chart-of-account") {
                if (response.success) {
                    message = response.success
                    message = message.charAt(0).toUpperCase() + message.slice(1)
                    toastr.success(message)
                    chartOfAccountTable.row.add([
                        response.data.uuid,
                        response.data.accountNameGroup,
                        response.data.accountValue,
                        response.data.accountName,
                        response.data.editBtn,
                        response.data.excludeBtn
                    ]).draw(false)

                    $("#chartOfAccountGroupSelect").val(null).trigger("change")
                    resetInputValues.forEach(function(element) {
                        element.value = ""
                    })
                }
            } else {
                if (response.success) {
                    modal.style.display = "flex"
                    window.location.href = response.url
                }
            }
        })
    })

    if (chartOfAccountTable) {
        const launchModal = $("#launchModal")
        chartOfAccountTable.on("click", ".trash-link", function(event) {
            event.preventDefault()
            dataTransfer.uuid = $(this).data("uuid")
            dataTransfer.account_name = $(this).data("accountname")
            dataTransfer.row = $(this).closest("td").closest("tr")
            launchModal.click()
        })

        const dismissModal = $("#dismissModal")
        const saveChanges = $("#saveChanges")
        launchModal.click(function() {
            $("#modalContainerLabel").html("Atenção!")
            $(".modal-body").html(`Deseja mesmo excluir a conta "${dataTransfer.account_name}"?`)
            dismissModal.html("Voltar")
            saveChanges.removeClass("btn-primary")
            saveChanges.addClass("btn-danger")
            saveChanges.html("Excluir")
        })

        saveChanges.click(function() {
            showSpinner(this)

            const url = window.location.origin + "/admin/balance-sheet/chart-of-account/delete"
            const form = new FormData()
            form.append("uuid", dataTransfer.uuid)

            fetch(url, {
                method: "POST",
                body: form
            }).then(response => response.json()).then(function(response) {
                saveChanges.removeAttr("disabled")
                saveChanges.html("Excluir")
                dismissModal.click()
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
                    chartOfAccountTable.row(dataTransfer.row).remove().draw(false)
                }
            })
        })
    }
}