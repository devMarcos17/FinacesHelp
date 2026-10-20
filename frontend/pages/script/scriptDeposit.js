const API_URL = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? 'http://localhost:8000'
    : 'https://finaceshelp.onrender.com';
    
async function deposit(event, id) {
    event.preventDefault();

    const token = 'Bearer ' + sessionStorage.getItem('session');
    const form = event.target;
    const formData = new FormData(form);

    formData.append('id', id);

    try {
        const response = await fetch(
            `${API_URL}/api/goals/deposit`,
            {
                method: 'POST',
                headers: {
                    'Authorization': token,
                    'Accept':'application/json',
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
