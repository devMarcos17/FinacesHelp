async function filterUsersMonth() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    try {
        const response = await fetch('http://127.0.0.1:8000/api/auth/filterUsersMonth', {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json',
            },
        });
        const data = await response.json();
        if (response.ok) {
            const listContainer = document.getElementById('list-filterUsers');
            if (!listContainer) return;

            listContainer.innerHTML = '';

            data.users.forEach(user => {
                listContainer.innerHTML += `
            <div class="user-card">
                <p><strong>ID:</strong> ${user.id}</p>
                <p><strong>Nome:</strong> ${user.name}</p>
                <p><strong>E-mail:</strong> ${user.email}</p>
                <p><strong>Telefone:</strong> ${user.phone}</p>
                <p><strong>Status:</strong> ${user.status}</p>
            </div>
        `;
            });

        }
    } catch (error) {
        console.log(error);
    }

}
filterUsersMonth();