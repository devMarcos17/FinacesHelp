const API_URL = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? 'http://localhost:8000'
    : 'https://finaceshelp.onrender.com';

async function logout()
{
    const token = 'Bearer ' + sessionStorage.getItem('session');
    const response = await fetch(`${API_URL}/api/auth/logout`,{
        method:'POST',
        headers:{
            'Authorization':token,
            'Accept':'application/json',
        },
    });
    if(response.ok){
        alert('Voce foi desconectado!');
        sessionStorage.removeItem('session');
        window.location.assign('login.html');

        console.log(new URL('login.html', window.location.href).href);
    }
}