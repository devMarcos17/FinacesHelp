    async function resetPassword() {
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');
        const email = urlParams.get('email');
        if (!token || !email) {
            alert('Link inválido ou expirado.');
        }

        const form = document.getElementById('formReset');
        form.addEventListener('submit', async event => {
            event.preventDefault();
            const formData = new FormData(form);
            const payload = {
                ...Object.fromEntries(formData),
                email: email,
                token: token
            };
            try {
                const response = await fetch('http://127.0.0.1:8000/api/auth/reset', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                //const data = await response.json();
                if (response.ok) {
                    alert('Senha atualizada com sucesso!');
                    console.log('Senha redefinida com sucesso!');
                    window.location.href = '../pagesUser/login.html';
                }
                else {
                    alert('Error ao redefinir a senha');
                }
            } catch (error) {
                console.log(error);
            }
        });
    }
    resetPassword();