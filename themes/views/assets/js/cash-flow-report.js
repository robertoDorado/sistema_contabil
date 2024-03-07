if (window.location.pathname == "/admin/cash-flow/report") {
    const jsonMessage = document.getElementById("jsonMessage")
    const urlJson = document.getElementById("urlJson").dataset.url

    let message = {
        cash_flow_empty: ''
    }

    if (jsonMessage) {
        message = JSON.parse(jsonMessage.dataset.message)
        message.cash_flow_empty = message.cash_flow_empty.charAt(0).toUpperCase()
            + message.cash_flow_empty.slice(1)
    }

    const table = $("#cashFlowReport").DataTable({
        "order": [[0, "desc"]],
        "language": {
            "url": urlJson,
            "emptyTable": message.cash_flow_empty,
        },
        "responsive": true,
        "lengthChange": false, 
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
        "initComplete": function () {
            this.api()
                .buttons()
                .container()
                .appendTo("#widgets .col-md-6:eq(0)");
        }
    })

    const tFoot = document.querySelector("tfoot").firstElementChild
    table.on('search.dt', function() {
        const dataFilter = table.rows({ search: 'applied' }).data();
        if (table.search()) {
            let balance = 0
            dataFilter.each(function(row) {
                let entryValue = parseFloat(row[4].replace("R$", "")
                    .replace(".", "").replace(",", ".").trim())
                
                if (row[3] == 'Débito') {
                    entryValue = entryValue * -1
                }

                balance += entryValue
            })

            balance < 0 ? tFoot.style.color = "#ff0000" : balance == 0 ?
                tFoot.removeAttribute("style") : tFoot.style.color = "#008000"
            
            if (balance < 0) {
                tFoot.children[4].innerHTML = (balance * -1)
                    .toLocaleString("pt-br", {"currency": "BRL", "style": "currency"})
            }else {
                tFoot.children[4].innerHTML = balance
                    .toLocaleString("pt-br", {"currency": "BRL", "style": "currency"})
            }
        }
    })
}