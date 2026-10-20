const API_URL = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? 'http://localhost:8000'
    : 'https://finaceshelp.onrender.com';

async function getActiveUser() {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch(
            `${API_URL}/api/auth/getActiveUsers`,
            {
                method: 'GET',
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json',
                },
            }
        );

        const data = await response.json();

        if (!response.ok) {
            console.error('Erro ao buscar usuários:', data);
            return;
        }

        const usersContainer = document.getElementById("users");
        if (!usersContainer) return;

        usersContainer.innerHTML = '';

        data.users.forEach(user => {
            const div = document.createElement("div");

            div.innerHTML = `
                <p>
                    <strong>ID:</strong> ${user.id}
                    <strong>Nome:</strong> ${user.name}
                    <br>
                    <strong>Email:</strong> ${user.email}
                    <br>
                    <strong>Tel:</strong> ${user.phone}
                    <br>
                    <p><strong>Status:</strong> <span class="status-inativo">${user.status}</span></p>
                    <br>

                    <button
                        type="button"
                        class="btn-active"
                        onclick="activeUser(${user.id})">
                        Desativar
                    </button>
                </p>
                <hr>
            `;

            usersContainer.appendChild(div);
        });

    } catch (error) {
        console.error('Erro:', error);
    }
}


async function activeUser(id) {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch(
            `${API_URL}/api/auth/disable`,
            {
                method: 'POST',
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: id
                }),
            }
        );

        const data = await response.json();

        console.log('Resposta da API:', data);

        if (!response.ok) {
            console.error('Erro ao ativar usuário:', data);
            alert('Erro ao desativar usuário.');
            return;
        }

        alert('Usuário desativado com sucesso!');

        getActiveUser();

    } catch (error) {
        console.error('Erro:', error);
    }
}


async function createPdfUserActive() {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch(`${API_URL}/api/auth/createPdfUserActive`, {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/pdf',
            },
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Detalhes do erro do servidor:', errorText);
            throw new Error('Erro ao gerar o PDF no servidor');
        }

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);

        const a = document.createElement('a');
        a.href = url;
        a.download = 'pdf-listagem-usuario.pdf';
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);

        alert('Baixado com sucesso!');
    } catch (error) {
        console.error('Erro na requisição:', error);
        alert('Erro ao baixar o arquivo.');
    }
}

getActiveUser();