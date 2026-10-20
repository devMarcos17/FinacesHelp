const API_URL = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? 'http://localhost:8000'
    : 'https://finaceshelp.onrender.com';

const form = document.querySelector('#form');

form.addEventListener('submit', async event => {
    event.preventDefault();

    const formData = new FormData(form);

    try {
        const response = await fetch(`${API_URL}/api/auth/register`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();

        console.log('Status:', response.status);
        console.log('Resposta:', data);

        if (response.ok) {
            alert('Cadastrado com sucesso!');
            window.location.href = 'login.html';
        } else {
            alert(data.message || JSON.stringify(data.errors));
        }

    } catch (error) {
        console.error('Erro:', error);
    }
});