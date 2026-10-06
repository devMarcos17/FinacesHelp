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

    console.log(data.transaction);

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

async function highestMonthlyExpense() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    try {
        const response = await fetch('http://127.0.0.1:8000/api/transaction/highestMonthlyExpense', {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (response.ok) {
            document.getElementById('highest-expense').innerText = 'R$ ' + data.transaction.amount;
            document.getElementById('highest-expense-description').innerText = data.transaction.description.toUpperCase();
            document.getElementById('highest-expense-payment').innerText = data.transaction.payment_method.toUpperCase();
        }
    } catch (error) {
        console.log(error);
    }
}

async function highestMonthlyRevenue() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    try {
        const response = await fetch('http://127.0.0.1:8000/api/transaction/highestMonthlyRevenue', {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (response.ok) {
            console.log(data.transaction.amount);
            document.getElementById('highest-revenue').innerText = 'R$ ' + data.transaction.amount;
            document.getElementById('highest-revenue-description').innerText = data.transaction.description.toUpperCase();
            document.getElementById('highest-revenue-payment').innerText = data.transaction.payment_method.toUpperCase();
        }
    } catch (error) {
        console.log(error);
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
                        label: function (context) {

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
highestMonthlyExpense();
highestMonthlyRevenue();
