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
            console.log(data.users);
             document.getElementById('user-name').textContent =
                `Olá, ${data.name}!`;

        }
    } catch (error) {
        console.log(error);
    }

}

getMe();