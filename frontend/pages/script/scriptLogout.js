async function logout()
{
    const token = 'Bearer ' + sessionStorage.getItem('session');
    const response = await fetch('http://127.0.0.1:8000/api/auth/logout',{
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