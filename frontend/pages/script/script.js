async function register() {
    const form = document.querySelector('#form');
    if (!form) return;

    form.addEventListener('submit', async event => {
        event.preventDefault();

        const formData = new FormData(form);
        
        try {
            const response = await fetch(`${API_URL}/api/auth/register`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

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