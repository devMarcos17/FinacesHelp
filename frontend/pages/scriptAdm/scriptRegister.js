async function register() {
    const form = document.querySelector('#form');

    form.addEventListener('submit', async event => {
        event.preventDefault();

        const formData = new FormData(form);

        const date = formData.get('date_of_birt');

        if (date) {
            const [year, month, day] = date.split('-');
            formData.set('date_of_birt', `${day}/${month}/${year}`);
        }

        console.log('Enviando:', Object.fromEntries(formData));

        try {
            const response = await fetch('http://127.0.0.1:8000/api/auth/register', {
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
            } else {
                alert(data.message || JSON.stringify(data.errors));
            }

        } catch (error) {
            console.error('Erro:', error);
        }
    });
}
register();