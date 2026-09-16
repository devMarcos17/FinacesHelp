async function update() {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');

    const form = document.getElementById('updateForm');

    if (!form) {
        console.log('Formulário updateForm não encontrado!');
        return;
    }

    document.getElementById('id').value = id;

    form.addEventListener('submit', async event => {
        event.preventDefault();

        const formData = new FormData(form);

        try {
            const formData = new FormData(form);

            const response = await fetch(
                'http://127.0.0.1:8000/api/auth/update',
                {
                    method: 'PUT',
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    },
                    body: formData
                }
            );

            const data = await response.json();

            if (response.ok) {
                alert('Atualizado com sucesso!');
                console.log(data);
            } else {
                console.log('Falha ao atualizar:', data);
            }

        } catch (error) {
            console.log('Erro:', error);
        }
    });
}

update();