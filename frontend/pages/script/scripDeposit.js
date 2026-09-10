async function deposit(event, id) {
    event.preventDefault(); // Isto agora vai funcionar porque o onsubmit HTML vai capturá-lo

    const token = 'Bearer ' + sessionStorage.getItem('session');
    const form = event.target;
    const formData = new FormData(form);

    formData.append('id', id);

    try {
        const response = await fetch(
            'http://127.0.0.1:8000/api/goals/deposit',
            {
                method: 'POST',
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json'
                },
                body: formData
            }
        );

        const data = await response.json();

        if (response.ok) {
            alert('Depósito realizado com sucesso!');
            console.log(data);
            window.location.reload();
        } else {
            alert('Falha ao realizar depósito');
            console.log(data);
        }

    } catch (error) {
        console.log('Erro:', error);
    }
}
