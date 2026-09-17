async function forgout()
{
    const token = 'Bearer ' + sessionStorage.getItem('session');

    const form = document.getElementById('formForgot');
    form.addEventListener('submit', async event=>{
        event.preventDefault();
        const formData = new FormData(form);
        try{
        const response = await fetch('http://127.0.0.1:8000/api/auth/forgot',{
            method:'POST',
            headers:{
                'Authorization':token,
                'Accept':'application/json',
            },
            body:formData
        });
        const data = await response.json();
        if(response.ok){
            alert('Email enviado com sucesso!');
        }
        else{
            alert("Falha ao enviar o email");
        }
    }catch(error){
        console.log(error);
    }
      
    });
    
}
forgout();