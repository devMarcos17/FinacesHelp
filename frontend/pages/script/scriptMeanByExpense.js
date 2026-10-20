const API_URL = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? 'http://localhost:8000'
    : 'https://finaceshelp.onrender.com';

async function meanByDayExpense() {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch(
            `${API_URL}/api/transaction/meanByDayExpense`,
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
            throw new Error('Erro ao buscar média diária de despesas');
        }

        document.getElementById('mean-day-expense').textContent =
            formatCurrency(data.transaction.avg);

    } catch (error) {
        console.error(error);
    }
}

async function meanByMonthExpense() {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch(
            `${API_URL}/api/transaction/meanByMonthExpense`,
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
            throw new Error('Erro ao buscar média mensal de despesas');
        }

        document.getElementById('mean-month-expense').textContent =
            formatCurrency(data.transaction.avg);

    } catch (error) {
        console.error(error);
    }
}

async function meanByYearExpense() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    try {
        const response = await fetch(`${API_URL}/api/transaction/meanByMonthExpense`, {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error('Error ao buscar a média anual');
        }
        document.getElementById('year-month-expense').textContent =
            formatCurrency(data.transaction.avg);
    } catch (error) {
        console.log(error);
    }
}

function formatCurrency(value) {
    return Number(value).toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });
}

meanByDayExpense();
meanByMonthExpense();
meanByYearExpense();