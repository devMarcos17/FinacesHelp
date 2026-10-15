async function projectionInBalance() 
{
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch(
            'http://127.0.0.1:8000/api/transaction/projection',
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
            throw new Error('Erro ao buscar a estimativa');
        }

        document.getElementById('projection-expense').textContent = formatCurrency(data.transaction);

    } catch (error) {
        console.error(error);
    }
}
projectionInBalance();