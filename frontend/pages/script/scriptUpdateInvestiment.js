async function update() {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');

    const form = document.getElementById('investimentForm');

    if (!form) {
        return;
    }

    const idInput = document.getElementById('id');

    if (!idInput) {
        console.error('Input #id não encontrado');
        return;
    }

    idInput.value = id;

    form.addEventListener('submit', async event => {
        event.preventDefault();

        const formData = new FormData(form);

        try {
            const response = await fetch(
                'http://127.0.0.1:8000/api/investiment/update',
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

            console.log(data);

            if (response.ok) {
                alert('Atualizado com sucesso!');
            } else {
                alert('Falha ao atualizar!');
                console.log(data);
            }

        } catch (error) {
            console.error('Erro:', error);
        }
    });
}

update();
