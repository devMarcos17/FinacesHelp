const form = document.getElementById('formLogin');
form.addEventListener('submit', async event => {
    event.preventDefault();
    const formData = new FormData(form);
    try {
        const response = await fetch('http://127.0.0.1:8000/api/auth/login', {
            method: 'post',
            mode: 'cors',
            body: formData
        });
        const data = await response.json();
        if (response.ok) {
            sessionStorage.setItem('session', data.access_token);
            alert('Autenticado com sucesso!');
            window.location.href = '../pagesUser/index.html'
        }
        else {
            alert('Credenciais inválidas');
        }
    } catch (error) {
        console.log(error);
    }

})