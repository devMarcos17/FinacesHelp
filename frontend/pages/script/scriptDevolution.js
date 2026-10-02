async function devolution(event, id) {
    event.preventDefault();

    const token = 'Bearer ' + sessionStorage.getItem('session');
    const form = event.target;
    const formData = new FormData(form);

    try {
        const response = await fetch('http://127.0.0.1:8000/api/goals/withDraw', {
            method: 'POST',
            headers: {
                'Authorization': token,
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();

        if (response.ok) {
            alert('Retirada realizada com sucesso!');
            window.location.reload(); 
        } else {
            console.log(data);
            alert('Falha ao realizar retirada');
        }
    } catch (error) {
        console.error('Erro:', error);
    }
}