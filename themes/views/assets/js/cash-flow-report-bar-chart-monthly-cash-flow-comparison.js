if (window.location.pathname == "/admin/analyzes-and-indicators/cash-flow/charts-and-visualizations") {

    fetch(window.location.origin + "/admin/analyzes-and-indicators/cash-flow/chart-bar-data/monthly-cash-flow-comparison" + window.location.search)
    .then(response => response.json()).then(function(response) {
        if (response.data) {
            const positiveValues = []
            const negativeValues = []
            const allMonths = []

            for(let i = 0; i < response.data.length; i++) {
                allMonths.push(response.data[i].month_name)
                positiveValues.push(response.data[i].positive_value)
                negativeValues.push(response.data[i].negative_value)
            }

            const monthDate = {
                labels: allMonths,
                datasets: [
                    {
                        label: 'Receitas',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        data: positiveValues
                    },
                    {
                        label: 'Despesas',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        data: negativeValues
                    }
                ]
            };
            
            // Configuração do Gráfico de Comparação Mensal
            const monthlyComparasion = {
                type: 'bar',
                data: monthDate,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };
            
            const ctxMonth = document.getElementById('monthlyComparasion').getContext('2d');
            new Chart(ctxMonth, monthlyComparasion);
        }
    
    })
}