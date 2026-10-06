async function deleteInvestiment(id) {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch(
            'http://127.0.0.1:8000/api/investiment/delete',
            {
                method: 'DELETE',
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: id
                })
            }
        );

        const data = await response.json();

        if (response.ok) {
            alert('Investimento excluído com sucesso!');
            window.location.reload();
        } else {
            console.log('Erro ao excluir:', data);
        }

    } catch (error) {
        console.log('Erro:', error);
    }
}