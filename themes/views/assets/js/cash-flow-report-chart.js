if (window.location.pathname == "/admin/cash-flow/report") {
    
    fetch(window.location.origin + "/admin/cash-flow/chart-data" + window.location.search)
    .then(response => response.json()).then(function(response) {
        const containerChartLine = document.getElementById("containerChartLine")

        if (response.created_at && response.entry) {
            containerChartLine.style.display = "block"
            const financeData = {
                labels: response.created_at,
                datasets: [{
                    label: "Projeção financeira",
                    data: response.entry,
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 1,
                    fill: false
                }]
            };
        
            const chartOptions = {
                scales: {
                    y: {
                        title: {
                            display: true,
                            text: 'Valor Financeiro'
                        },
                        ticks: {
                            autoSkip: true,
                            maxTicksLimit: 10,
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tempo (Dias)'
                        },
                        ticks: {
                            autoSkip: true,
                            maxTicksLimit: 10
                        }
                    }
                }
            };
        
            const ctx = document.getElementById('chartCashFlowReport').getContext('2d');
             new Chart(ctx, {
                type: 'line',
                data: financeData,
                options: chartOptions
            });

        }
    })

}