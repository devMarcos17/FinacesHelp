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
         document.getElementById('balance').textContent = dataBalance.balance;
        document.getElementById('expense').textContent = dataExpense.total_expense;
        document.getElementById('revenue').textContent = dataRevenue.total_revenue;
    }
}

getTransaction();




async function getFilteredTransactions() {

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

const transactionsContainer = document.getElementById('transactions');

data.transactions.forEach(transaction => {
    transactionsContainer.innerHTML += `
        <div>
            <p>Categoria: ${transaction.category}</p>
            <p>Valor: ${transaction.amount}</p>
            <p>Tipo: ${transaction.type}</p>
            <p>Descrição: ${transaction.description}</p>
        </div>
    `;
});
}

document.getElementById('filterButton').addEventListener(
    'click',
    getFilteredTransactions
);


