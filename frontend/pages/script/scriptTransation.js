async function getTransaction() {

    const token = 'Bearer ' + sessionStorage.getItem('session');

    const [responseRevenue, responseExpense, responseBalance] = await Promise.all([

        fetch('http://127.0.0.1:8000/api/transaction/totalRevenue', {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json',
            }
        }),

        fetch('http://127.0.0.1:8000/api/transaction/totalExpense', {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json',
            }
        }),

        fetch('http://127.0.0.1:8000/api/transaction/balance', {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json',
            }
        })

    ]);

    const dataBalance = await responseBalance.json();
    const dataExpense = await responseExpense.json();
    const dataRevenue = await responseRevenue.json();

    if (responseBalance.ok && dataExpense) {

        document.getElementById('balance').textContent =
            dataBalance.balance;

        document.getElementById('expense').textContent =
            dataExpense.total_expense;

        document.getElementById('revenue').textContent =
            dataRevenue.total_revenue;
    }
}

getTransaction();

async function getFilteredTransactionsMonth() {

    const token = 'Bearer ' + sessionStorage.getItem('session');

    const month = document.getElementById('month').value;

    const response = await fetch(
        'http://127.0.0.1:8000/api/transaction/filter',
        {
            method: 'POST',

            headers: {
                'Authorization': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },

            body: JSON.stringify({
                month: month
            })
        }
    );

    const data = await response.json();

    console.log('Filtro mês:', data);

    if (!response.ok) {
        console.log('Erro:', data);
        return;
    }

    const transactionsContainer =
        document.getElementById('transactions');

    transactionsContainer.innerHTML = '';

    data.transactions.forEach(transaction => {

        transactionsContainer.innerHTML += `
                <div>
                    <p>Categoria: ${transaction.category}</p>
                    <p>Valor: ${transaction.amount}</p>
                    <p>Tipo: ${transaction.type}</p>
                    <p>Descrição: ${transaction.description}</p>
                    <p>Forma de pagamento: ${transaction.payment_method}</p>
                </div>
            `;
    });
}

async function getFilteredTransactionsType() {

    const token = 'Bearer ' + sessionStorage.getItem('session');

    const type = document.getElementById('revenue-expense').value;

    if (!type) {
        return;
    }

    let url;

    if (type === 'expense') {

        url = 'http://127.0.0.1:8000/api/transaction/filterExpense';

    }

    if (type === 'revenue') {

        url = 'http://127.0.0.1:8000/api/transaction/filterRevenue';

    }

    const response = await fetch(url, {

        method: 'GET',

        headers: {
            'Authorization': token,
            'Accept': 'application/json'
        }

    });

    const data = await response.json();

    console.log('Filtro tipo:', data);

    if (!response.ok) {

        console.log('Erro:', data);

        return;
    }

    const transactionsContainer =
        document.getElementById('transactions');

    transactionsContainer.innerHTML = '';

    data.transactions.forEach(transaction => {

        transactionsContainer.innerHTML += `
                <div>
                    <p>Categoria: ${transaction.category}</p>
                    <p>Valor: R$ ${transaction.amount}</p>
                    <p>Tipo: ${transaction.type}</p>
                    <p>Descrição: ${transaction.description}</p>
                </div>
            `;
    });
}


async function getFilterCategory() {

    const token = 'Bearer ' + sessionStorage.getItem('session');

    const category =
        document.getElementById('category').value;

    console.log('Categoria selecionada:', category);

    if (!category) {

        console.log('Nenhuma categoria selecionada');

        return;
    }

    const response = await fetch(
        'http://127.0.0.1:8000/api/transaction/expensesCategories',
        {
            method: 'POST',

            headers: {
                'Authorization': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },

            body: JSON.stringify({
                category: category
            })
        }
    );

    const data = await response.json();

    console.log('Status:', response.status);
    console.log('Resposta da API:', data);

    if (!response.ok) {

        console.log('ERRO NA API:', data);

        return;
    }

    const transactionsContainer =
        document.getElementById('transactions');

    transactionsContainer.innerHTML = '';

    data.transactions.forEach(transaction => {

        transactionsContainer.innerHTML += `
                <div>
                    <p>Categoria: ${transaction.category}</p>
                    <p>Valor: R$ ${transaction.amount}</p>
                    <p>Tipo: ${transaction.type}</p>
                    <p>Descrição: ${transaction.description}</p>
                </div>
            `;
    });
}

document
    .getElementById('filterButton')
    .addEventListener('click', () => {

        const month =
            document.getElementById('month').value;

        const type =
            document.getElementById('revenue-expense').value;

        const category =
            document.getElementById('category').value;


        if (category) {

            getFilterCategory();

        } else if (type) {

            getFilteredTransactionsType();

        } else if (month) {

            getFilteredTransactionsMonth();

        } else {

            console.log('Nenhum filtro selecionado');

        }

    });

async function getExpenses() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    const category =
        document.getElementById('expense-month-button').value;
    const response = await fetch(
        'http://127.0.0.1:8000/api/transaction/expensesByCategory',
        {
            method: 'POST',

            headers: {
                'Authorization': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },

            body: JSON.stringify({
                category: category
            }),
        });
    const data = await response.json();

    console.log('Status:', response.status);
    console.log('Resposta da API:', data);

    if (!response.ok) {

        console.log('ERRO NA API:', data);

        return;
    }

    const transactionsContainer =
        document.getElementById('transactions');

    transactionsContainer.innerHTML = '';

    data.transactions.forEach(transaction => {

        transactionsContainer.innerHTML += `
                <div>
                    <p>Categoria: ${transaction.category}</p>
                    <p>Valor: R$ ${transaction.amount}</p>
                    <p>Tipo: ${transaction.type}</p>
                    <p>Descrição: ${transaction.description}</p>
                </div>
            `;
    });
}

let chartRevenueMonth = null;
async function getRevenueMounth() {

    const token = 'Bearer ' + sessionStorage.getItem('session');

    const month =
        document.getElementById('revenue-month-filter').value;

    if (!month) {
        return;
    }

    const response = await fetch(
        'http://127.0.0.1:8000/api/transaction/monthlyRevenue',
        {
            method: 'POST',

            headers: {
                'Authorization': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },

            body: JSON.stringify({
                month: month
            })
        }
    );

    const data = await response.json();

    console.log('Status:', response.status);
    console.log('Resposta da API:', data);

    if (!response.ok) {
        console.log('ERRO NA API:', data);
        return;
    }

    const revenueMonthResult =
        document.getElementById('revenue-month-result');

    revenueMonthResult.innerHTML = `
            <p>Total recebido no mês: R$ ${data.transactions}</p>
        `;

    const canvas = document.getElementById('revenue-month-chart');
    if (canvas) {
        if (chartRevenueMonth) {
            chartRevenueMonth.destroy();
        }
        chartRevenueMonth = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: [`Mês ${month}`],
                datasets: [{
                    label: 'Receita Total',
                    data: [Number(data.transactions)],
                    backgroundColor: '#01ac40'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
}

document
    .getElementById('revenue-month-button')
    .addEventListener('click', getRevenueMounth);


// ==========================================
// GASTOS POR MÊS
// ==========================================

let chartExpensesMonth = null;
async function getExpensesMonth() {

    const token = 'Bearer ' + sessionStorage.getItem('session');

    const month =
        document.getElementById('expense-month-filter').value;

    if (!month) {
        return;
    }

    const response = await fetch(
        'http://127.0.0.1:8000/api/transaction/monthlyExpense',
        {
            method: 'POST',

            headers: {
                'Authorization': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },

            body: JSON.stringify({
                month: month
            })
        }
    );

    const data = await response.json();

    console.log('Status:', response.status);
    console.log('Resposta da API:', data);

    if (!response.ok) {
        console.log('ERRO NA API:', data);
        return;
    }

    const expenseMonthResult =
        document.getElementById('expense-month-result');

    expenseMonthResult.innerHTML = `
            <p>Total gasto no mês: R$ ${data.transactions}</p>
        `;

    const canvas = document.getElementById('expense-month-chart');
    if (canvas) {
        if (chartExpensesMonth) {
            chartExpensesMonth.destroy();
        }
        chartExpensesMonth = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: [`Mês ${month}`],
                datasets: [{
                    label: 'Gasto Total',
                    data: [Number(data.transactions)],
                    backgroundColor: '#e10c0c'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
}

document
    .getElementById('expense-month-button')
    .addEventListener('click', getExpensesMonth);

let chartExpenseCategories = null;
async function getExpenseCategories() {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    const month = document.getElementById('expense-category-filter').value;

    if (!month) {
        return;
    }

    try {
        const response = await fetch(
            'http://127.0.0.1:8000/api/transaction/expensesByCategory',
            {
                method: 'POST',

                headers: {
                    'Authorization': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },

                body: JSON.stringify({
                    month: month
                })
            }
        );

        const data = await response.json();

        console.log('Status:', response.status);
        console.log('Resposta da API:', data);

        if (!response.ok) {
            console.log('ERRO NA API:', data);

            document.getElementById('expense-category-result').innerHTML = `
                <p>Erro ao buscar os gastos.</p>
            `;

            return;
        }

        const result = document.getElementById('expense-category-result');

        result.innerHTML = '';

        data.transaction.forEach(item => {
            result.innerHTML += `
                <p>
                    Categoria: ${item.category}
                    - Total: R$ ${item.total}
                </p>
            `;
        });

        const canvas = document.getElementById('expense-category-chart');
        if (canvas) {
            if (chartExpenseCategories) {
                chartExpenseCategories.destroy();
            }

            const labels = data.transaction.map(item => item.category);
            const amounts = data.transaction.map(item => Number(item.total));

            chartExpenseCategories = new Chart(canvas, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: amounts,
                        backgroundColor: [
                            '#e10c0c', '#2563eb', '#f59e0b', '#10b981',
                            '#8b5cf6', '#ec4899', '#64748b', '#06b6d4'
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

    } catch (error) {
        console.error('Erro na requisição:', error);

        document.getElementById('expense-category-result').innerHTML = `
            <p>Erro ao conectar com a API.</p>
        `;
    }
}

document
    .getElementById('expense-category-button')
    .addEventListener('click', getExpenseCategories);


async function createTransaction() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    const formTransaction = document.getElementById('formTransaction');
    formTransaction.addEventListener('submit', async event => {
        event.preventDefault();
        const formDataTransaction = new FormData(formTransaction);
        try {
            const responseTransaction = await fetch('http://127.0.0.1:8000/api/transaction/create', {
                method: 'POST',
                headers: {
                    'Authorization': token,
                },
                body: formDataTransaction
            });
            const dataTransaction = await responseTransaction.json();
            if (responseTransaction.ok) {
                alert('Transação criada com sucesso!');
                console.log('transação criada com sucesso!');
            }
            else {
                alert('Falha ao criar');
            }
        } catch (error) {
            console.log(error);
        }
    });
}
createTransaction();