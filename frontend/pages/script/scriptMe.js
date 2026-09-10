async function getMe() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    try {
        const response = await fetch('http://127.0.0.1:8000/api/auth/me', {
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