if (window.location.pathname == "/admin/cash-flow/report") {
    $(function () {
        $("#cashFlowReport").DataTable({
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