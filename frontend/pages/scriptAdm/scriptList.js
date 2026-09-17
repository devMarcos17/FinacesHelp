async function getList() {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch('http://127.0.0.1:8000/api/auth/list', {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (response.ok) {
            renderUsersList(data.user);
        } else {
            console.error('Erro na requisição:', data);
        }
    } catch (error) {
        console.error('Erro de conexão:', error);
    }
}

async function searchUsers(query) {
    if (!query || query.trim() === '') {
        getList();
        return;
    }

    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch('http://127.0.0.1:8000/api/auth/search', {
            method: 'POST',
            headers: {
                'Authorization': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                search: query
            })
        });

        const data = await response.json();

        if (response.ok) {
            const users = data.users || data.user || data;
            renderUsersList(users);
        } else {
            console.error('Erro na pesquisa:', data);
        }
    } catch (error) {
        console.error('Erro na pesquisa:', error);
    }
}

function renderUsersList(users) {
    const listContainer = document.getElementById('list-users');
    if (!listContainer) return;

    listContainer.innerHTML = '';

    users.forEach(user => {
        listContainer.innerHTML += `
            <div class="user-card">
                <div>
                    <p><strong>ID:</strong> ${user.id}</p>
                    <p><strong>Nome:</strong> ${user.name}</p>
                    <p><strong>E-mail:</strong> ${user.email}</p>
                    <p><strong>Telefone:</strong> ${user.phone}</p>
                    <p><strong>Status:</strong> <span style="color: ${user.status === 'ativo' ? '#00aa00' : '#6c757d'}; font-weight: bold;">${user.status}</span></p>
                </div>
                <div class="user-actions">
                    <button type="button" class="btn-edit" onclick="window.location.href='update.html?id=${user.id}'">
                        Atualizar
                    </button>
                    <button type="button" class="btn-delete" onclick="deleteUser(${user.id})">
                        Excluir
                    </button>
                    <button type="button" class="btn-active" onclick="activeUser(${user.id})">
                        Ativar
                    </button>
                    <button type="button" class="btn-disable" onclick="disableUser(${user.id})">
                        Desativar
                    </button>
                </div>
            </div>
        `;
    });
}

const searchInput = document.getElementById('search-input');
if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        searchUsers(e.target.value);
    });
}

async function deleteUser(id) {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch(
            'http://127.0.0.1:8000/api/auth/delete',
            {
                method: 'DELETE',
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: id
                }),
            }
        );

        const data = await response.json();

        if (response.ok) {
            alert('Usuario deletado com sucesso!');
            window.location.reload();
        } else {
            console.log('Erro ao cancelar:', data);
        }

    } catch (error) {
        console.log('Erro:', error);
    }
}

async function activeUser(id) {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    try {
        const response = await fetch('http://127.0.0.1:8000/api/auth/active', {
            method: 'POST',
            headers: {
                'Authorization': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id
            }),
        });

        const data = await response.json();
        if (response.ok) {
            console.log('Usuario ativado com sucesso!');
            alert('Usuario ativado com sucesso!');
            getList();
        }
    } catch (error) {
        console.log(error);
    }
}

async function disableUser(id) {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    try {
        const response = await fetch('http://127.0.0.1:8000/api/auth/disable', {
            method: 'POST',
            headers: {
                'Authorization': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id
            }),
        });

        const data = await response.json();
        if (response.ok) {
            console.log('Usuario desativado com sucesso!');
            alert('Usuario desativado com sucesso!');
            getList();
        }
    } catch (error) {
        console.log(error);
    }
}

getList();