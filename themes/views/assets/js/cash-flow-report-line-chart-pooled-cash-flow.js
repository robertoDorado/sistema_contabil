if (window.location.pathname == "/admin/analyzes-and-indicators/cash-flow/charts-and-visualizations") {
    document.querySelector(".content").style.padding = "2.1rem .5rem"
    fetch(window.location.origin + "/admin/analyzes-and-indicators/cash-flow/chart-line-data/pooled-cash-flow" + window.location.search)
    .then(response => response.json()).then(function(response) {
        const containerChartLine = document.getElementById("containerChartLine")

        if (response.created_at && response.entry) {
            containerChartLine.style.display = "block"
            const financeData = {
                labels: response.created_at,
                datasets: [{
                    label: "Fluxo de caixa agrupado por dia",
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
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tempo (Dias)'
                        }
                    }
                }
            };
        
            const ctx = document.getElementById('lineChartCashFlowReport').getContext('2d');
             new Chart(ctx, {
                type: 'line',
                data: financeData,
                options: chartOptions
            });

        }else {
            toastr.error("Esta empresa não possui movimento de caixa")
        }
    })

}