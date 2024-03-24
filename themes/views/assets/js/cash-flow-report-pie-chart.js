if (window.location.pathname == "/admin/cash-flow/report") {
     
    fetch(window.location.origin + "/admin/cash-flow/chart-pie-data")
    .then(response => response.json()).then(function(response) {
        const containerPieChart = document.getElementById("containerPieChart")
        if (response.total_accounts && response.accounts_data) {
            containerPieChart.style.display = "block"
            const data = {
               labels: response.accounts_data,
               datasets: [{
                   label: 'Grupo de contas',
                   data: response.total_accounts,
                   backgroundColor: [
                       'rgba(255, 99, 132, 0.5)',
                       'rgba(54, 162, 235, 0.5)',
                       'rgba(255, 206, 86, 0.5)',
                       'rgba(75, 192, 192, 0.5)',
                       'rgba(153, 102, 255, 0.5)'
                   ],
                   borderColor: [
                       'rgba(255, 99, 132, 1)',
                       'rgba(54, 162, 235, 1)',
                       'rgba(255, 206, 86, 1)',
                       'rgba(75, 192, 192, 1)',
                       'rgba(153, 102, 255, 1)'
                   ],
                   borderWidth: 1
               }]
           };
       
           const config = {
               type: 'pie',
               data: data,
               options: {
                   responsive: true,
                   title: {
                       display: true,
                       text: 'Gráfico de Pizza - Despesas Mensais'
                   }
               }
           };
       
           new Chart(
               document.getElementById('pieChartCashFlowReport'),
               config
           );
        }

    })
}