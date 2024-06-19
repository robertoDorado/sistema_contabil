if (window.location.pathname == "/admin/analyzes-and-indicators/cash-flow/charts-and-visualizations") {
    fetch(window.location.origin + "/admin/analyzes-and-indicators/cash-flow/chart-bar-data/expenses-by-account-group" + window.location.search)
    .then(response => response.json()).then(function(response) {
        if (response.data) {

            const accountGroup = []
            const totalValues = []

            for (let i = 0; i < response.data.length; i++) {
                accountGroup.push(response.data[i].group_name)
                totalValues.push(response.data[i].total_value)
            }
            console.log(totalValues)
            // Dados de Categorias de Despesas
            const dataCategory = {
                labels: accountGroup,
                datasets: [
                    {
                        label: 'Despesas por Categoria',
                        backgroundColor: ['#ff6384', '#36a2eb', '#cc65fe', '#ffce56', '#ff9f40', '#4bc0c0', '#9966ff'],
                        borderColor: '#fff',
                        data: totalValues
                    }
                ]
            };

            // Configuração do Gráfico de Categorias de Despesas
            const configExpenseCategories = {
                type: 'bar',
                data: dataCategory,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };

            const ctxCategories = document.getElementById('expenseCategories').getContext('2d');
            new Chart(ctxCategories, configExpenseCategories);
        }
    })
}