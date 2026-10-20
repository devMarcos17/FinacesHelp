async function payment() {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch(`${API_URL}/api/transaction/expensesByPayment`, {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json'
            },
        });

        const data = await response.json();
        
        if (response.ok) {
            const resultDiv = document.getElementById('expense-payment-result');
            resultDiv.innerHTML = '';

            data.transaction.forEach(transaction => {
                resultDiv.innerHTML += `<p>${transaction.payment_method}: R$ ${transaction.total}</p>`;
            });

            // Passando o ID específico do canvas de despesas
            createPaymentChart(data.transaction, 'expense-payment-chart');
        } else {
            console.log('error', data);
        }
    } catch (error) {
        console.log(error);
    }
}

async function paymentRevenue() {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch(`${API_URL}/api/transaction/revenuesByPayment`, {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json'
            },
        });

        const data = await response.json();
        
        if (response.ok) {
            const resultDiv = document.getElementById('revenue-payment-result');
            resultDiv.innerHTML = '';

            data.transaction.forEach(transaction => {
                resultDiv.innerHTML += `<p>${transaction.payment_method}: R$ ${transaction.total}</p>`;
            });
            createPaymentChart(data.transaction, 'revenue-payment-chart', true);
        } else {
            console.log('error', data);
        }
    } catch (error) {
        console.log(error);
    }
}

function createPaymentChart(transactions, canvasId, isRevenue = false) {
    const canvas = document.getElementById(canvasId);

    if (!canvas || typeof Chart === 'undefined') {
        console.error('Canvas não encontrado ou Chart.js não carregado:', canvasId);
        return;
    }

    const labels = transactions.map(item => item.payment_method);
    const amounts = transactions.map(item => Number(item.total));

    const expenseColors = [
        '#e10c0c',
        '#2563eb',
        '#f59e0b',
        '#10b981',
        '#8b5cf6',
        '#ec4899',
        '#64748b'
    ];

    const revenueColors = [
        '#16a34a',
        '#22c55e',
        '#15803d',
        '#4ade80',
        '#86efac',
        '#166534',
        '#bbf7d0'
    ];

    const colors = isRevenue ? revenueColors : expenseColors;

    const existingChart = Chart.getChart(canvas);

    if (existingChart) {
        existingChart.destroy();
    }

    new Chart(canvas, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: amounts,
                backgroundColor: labels.map(
                    (_, index) => colors[index % colors.length]
                ),
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = Number(context.raw);

                            return `${context.label}: R$ ${value.toFixed(2)}`;
                        }
                    }
                }
            }
        }
    });
}

payment();
paymentRevenue();