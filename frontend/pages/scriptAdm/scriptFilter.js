async function filterDataOld() {

    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {

        const response = await fetch(
            'http://127.0.0.1:8000/api/auth/filterDataOld',
            {
                method: 'GET',
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json',
                },
            }
        );

        const data = await response.json();

        if (response.ok) {

            const listContainer =
                document.getElementById('users-filters-old');

            data.users.forEach(user => {

                listContainer.innerHTML += `
                    <div class="user-card">

                        <div>

                            <p>
                                <strong>ID:</strong>
                                ${user.id}
                            </p>

                            <p>
                                <strong>Nome:</strong>
                                ${user.name}
                            </p>

                            <p>
                                <strong>E-mail:</strong>
                                ${user.email}
                            </p>

                            <p>
                                <strong>Telefone:</strong>
                                ${user.phone}
                            </p>

                            <p>
                                <strong>Status:</strong>

                                <span style="
                                    color: ${user.status === 'ativo'
                                        ? '#00aa00'
                                        : '#6c757d'
                                    };
                                    font-weight: bold;
                                ">
                                    ${user.status}
                                </span>

                            </p>

                        </div>

                    </div>
                `;

            });
        }

    } catch (error) {

        console.log(error);

    }
}


filterDataOld();


async function filterDataRecent() {

    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {

        const response = await fetch(
            'http://127.0.0.1:8000/api/auth/filterDataRecent',
            {
                method: 'GET',
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json',
                },
            }
        );

        const data = await response.json();

        if (response.ok) {

            const listContainer =
                document.getElementById('users-filters-recent');

            data.users.forEach(user => {

                listContainer.innerHTML += `
                    <div class="user-card">

                        <div>

                            <p>
                                <strong>ID:</strong>
                                ${user.id}
                            </p>

                            <p>
                                <strong>Nome:</strong>
                                ${user.name}
                            </p>

                            <p>
                                <strong>E-mail:</strong>
                                ${user.email}
                            </p>

                            <p>
                                <strong>Telefone:</strong>
                                ${user.phone}
                            </p>

                            <p>
                                <strong>Status:</strong>

                                <span style="
                                    color: ${user.status === 'ativo'
                                        ? '#00aa00'
                                        : '#6c757d'
                                    };
                                    font-weight: bold;
                                ">
                                    ${user.status}
                                </span>

                            </p>

                        </div>

                    </div>
                `;

            });
        }

    } catch (error) {

        console.log(error);

    }
}


filterDataRecent();
