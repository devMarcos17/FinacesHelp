async function getDashBoard() {

    const token = 'Bearer ' + sessionStorage.getItem('session');

    const response = await fetch(
        'http://127.0.0.1:8000/api/dashboard/dashboard',
        {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json'
            }
        }
    );

    const data = await response.json();

    console.log(data);

    if (response.ok) {

        document.getElementById('balance').textContent =
            `R$ ${data.summary.balance}`;

        document.getElementById('revenue').textContent =
            `R$ ${data.revenue}`;

        document.getElementById('expense').textContent =
            `R$ ${data.expense}`;

        createDashboardChart(data.revenue, data.expense);
    }
}


function createDashboardChart(revenue, expense) {

    const canvas = document.getElementById('dashboard-chart');

    new Chart(canvas, {
        type: 'pie',

        data: {
            labels: [
                'Receita',
                'Despesa'
            ],

            datasets: [{
                data: [
                    Number(revenue),
                    Number(expense)
                ],

                backgroundColor: [
                    '#01ac40', // verde = receita
                    '#e10c0c'  // vermelho = despesa
                ],

                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },

        options: {
            responsive: true,

            plugins: {
                legend: {
                    position: 'bottom'
                },

                tooltip: {
                    callbacks: {
                        label: function(context) {

                            const value = context.raw;

                            return `${context.label}: R$ ${value.toFixed(2)}`;
                        }
                    }
                }
            }
        }
    });
}

getDashBoard();
