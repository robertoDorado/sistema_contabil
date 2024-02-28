if (window.location.pathname == "/admin/cash-flow/report") {
    const jsonMessage = document.getElementById("jsonMessage")
    let message = {
        cash_flow_empty: ''
    }

    if (jsonMessage) {
        message = JSON.parse(jsonMessage.dataset.message)
        message.cash_flow_empty = message.cash_flow_empty.charAt(0).toUpperCase()
            + message.cash_flow_empty.slice(1)
    }

    $(function () {
        $("#cashFlowReport").DataTable({
            "language": {
                "emptyTable": message.cash_flow_empty,
            },
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": [
                {
                    extend: 'copy',
                    text: 'Copiar'
                },
                "csv",
                "excel",
                "pdf",
                "print",
                {
                    extend: 'colvis',
                    text: 'Visibilidade Coluna'
                }
            ]
        }).buttons().container().appendTo('#widgets .col-md-6:eq(0)');
    });
}