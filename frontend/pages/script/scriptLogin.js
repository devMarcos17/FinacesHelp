const API_URL = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? 'http://localhost:8000'
    : 'https://finaceshelp.onrender.com';

const form = document.getElementById('formLogin');
form.addEventListener('submit', async event => {
    event.preventDefault();
    const formData = new FormData(form);
    try {
        const response = await fetch(`${API_URL}/api/auth/login`, {
            method: 'post',
            mode: 'cors',
            body: formData
        });
        const data = await response.json();
        if (response.ok) {
            sessionStorage.setItem('session', data.access_token);
            alert('Autenticado com sucesso!');

            if (data.role === 'admin') {
                window.location.href = '../pagesAdmin/adm.html';
            } if(data.role === 'user'){
                window.location.href = '../pagesUser/index.html';
            }
        }
        else {
            alert('Credenciais inválidas');
        }
    } catch (error) {
        console.logln || console.log(error);
    }

});