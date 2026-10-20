async function fetchExpensesIncreased() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    const resultDiv = document.getElementById('expenses-increased-result');

    try {
        const response = await fetch(
            'http://127.0.0.1:8000/api/transaction/expensesIncresead',
            {
                method: 'GET',
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json'
                }
            }
        );

        const data = await response.json();

        if (!response.ok) {
            throw new Error('Erro ao buscar');
        }
        const percentage = data.transaction.percetage ?? data.transaction.percentage;
        resultDiv.innerHTML = `<p>Aumento de: <strong>${percentage}%</strong></p>`;

    } catch (error) {
        console.error(error);
        resultDiv.innerHTML = `<p style="color: red;">Erro ao carregar dados.</p>`;
    }
}

async function fetchRevenuesIncreased() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    const resultDiv = document.getElementById('revenues-increased-result');

    try {
        const response = await fetch(
            'http://127.0.0.1:8000/api/transaction/revenuesIncresead',
            {
                method: 'GET',
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json'
                }
            }
        );

        const data = await response.json();

        if (!response.ok) {
            throw new Error('Erro ao buscar');
        }

        const percentage = data.transaction.percetage ?? data.transaction.percentage;
        resultDiv.innerHTML = `<p>Aumento de: <strong>${percentage}%</strong></p>`;

    } catch (error) {
        console.error(error);
        resultDiv.innerHTML = `<p style="color: red;">Erro ao carregar dados.</p>`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    fetchExpensesIncreased();
    fetchRevenuesIncreased();
});