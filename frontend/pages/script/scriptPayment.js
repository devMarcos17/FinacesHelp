async function payment() {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch('http://127.0.0.1:8000/api/transaction/expensesByPayment', {
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

            createPaymentChart(data.transaction);
        }
        else {
            console.log('error', data);
        }
    } catch (error) {
        console.log(error);
    }
}

function createPaymentChart(transactions) {
    const canvas = document.getElementById('expense-payment-chart');
    if (!canvas) return;

    const labels = transactions.map(item => item.payment_method);
    const amounts = transactions.map(item => Number(item.total));

    const colors = [
        '#e10c0c',
        '#2563eb',
        '#f59e0b',
        '#10b981',
        '#8b5cf6',
        '#ec4899',
        '#64748b'
    ];

    new Chart(canvas, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: amounts,
                backgroundColor: colors.slice(0, labels.length),
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

payment();