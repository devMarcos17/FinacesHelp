async function getDisableUsers() {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch(
            'http://127.0.0.1:8000/api/auth/getDisableUsers',
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
                    <p><strong>Status:</strong> <span class="status-ativo">${user.status}</span></p>
                    <br>

                    <button
                        type="button"
                        class="btn-active"
                        onclick="disableUser(${user.id})">
                        Ativar
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


async function disableUser(id) {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch(
            'http://127.0.0.1:8000/api/auth/active',
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

        alert('Usuário ativado com sucesso!');

        getDisableUsers();

    } catch (error) {
        console.error('Erro:', error);
    }
}


getDisableUsers();
