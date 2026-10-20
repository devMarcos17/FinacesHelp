const API_URL = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? 'http://localhost:8000'
    : 'https://finaceshelp.onrender.com';

async function getMe() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    try {
        const response = await fetch(`${API_URL}/api/auth/me`, {
            method: 'post',
            headers: {
                'Authorization': token,
                'Accept': 'application/json',
            }
        });
        const data = await response.json();
        if (response.ok) {
             document.getElementById('user-name').textContent =
                `Olá, ${data.name}!`;

        }
    } catch (error) {
        console.log(error);
    }

}

getMe();