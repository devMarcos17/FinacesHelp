async function crateGoal() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    const form = document.getElementById('formGoal');
    form.addEventListener('submit', async event => {
        event.preventDefault();
        const formData = new FormData(form);
        try {
            const response = await fetch('http://127.0.0.1:8000/api/goals/create', {
                method: 'POST',
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json'
                },
                body: formData,
            });
            const data = await response.json();
            if (response.ok) {
                alert('Objetivo criado com sucesso!');
                console.log('Objetivo criado com sucesso!');
            }
            else {
                alert('Falha ao criar objetivo');
            }
            console.log(data);
        } catch (error) {
            console.log(error);
        }
    });
}
crateGoal();
async function listGoal() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    const resultado = document.getElementById('resultado');

    try {
        const response = await fetch('http://127.0.0.1:8000/api/goals/list', {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json'
            },
        });

        const data = await response.json();

        if (response.ok) {
            data.goal.forEach(goals => {
                if (goals.status == 'in_progress') {
                    let status = 'andamento';
                    resultado.innerHTML += `
            <div>
            <p>
            <strong>Título:</strong> ${goals.title}<br>
            <strong>Descrição:</strong> ${goals.description}<br>
            <strong>Status:</strong> ${status}<br>
            <img 
            src="http://127.0.0.1:8000/img/goals/${goals.image_path}" 
            alt="${goals.title}"
            style="width: 200px; height: auto;"
            >
            </p>

        <div class="progress-container">
            <div 
                class="progress-bar" 
                id="progress-${goals.id}"
                style="width: 0%">
            </div>
        </div>

        <p>
            Progresso: <span id="percentage-${goals.id}">0%</span>
        </p>

        <form class="deposit-form" onsubmit="deposit(event, ${goals.id})">
            <input type="hidden" name="id" value="${goals.id}">

            <label>Valor do depósito</label>
            <input 
                type="number" 
                name="current_amount" 
                step="0.01" 
                min="0.01" 
                placeholder="Ex: 500.00" 
                required
            >

            <button type="submit">Depositar</button>
        </form>

        <button type="button" onclick="deleteGoal(${goals.id})">
            Excluir
        </button>

        <button 
            type="button" 
            onclick="window.location.href='update.html?id=${goals.id}'"
        >
            Atualizar
        </button>

        <hr>
    </div>
`;
                    getGoalPercentage(goals.id);

                }
            });
        } else {
            resultado.textContent = 'Falha ao carregar API';
        }

    } catch (error) {
        resultado.textContent = 'Erro: ' + error;
    }
}
listGoal();
async function deleteGoal(id) {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch(
            'http://127.0.0.1:8000/api/goals/delete',
            {
                method: 'DELETE',
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: id
                })
            }
        );

        const data = await response.json();

        if (response.ok) {
            alert('Meta cancelada com sucesso!');

            // Volta/recarrega a mesma página
            window.location.reload();
        } else {
            console.log('Erro ao cancelar:', data);
        }

    } catch (error) {
        console.log('Erro:', error);
    }
}
async function getGoalPercentage(id) {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch(
            'http://127.0.0.1:8000/api/goals/progress',
            {
                method: 'POST',
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: id
                })
            }
        );

        const data = await response.json();

        if (response.ok) {
            const progressBar = document.getElementById(`progress-${id}`);
            const percentage = document.getElementById(`percentage-${id}`);

            console.log('ID enviado:', id);
            console.log('Resposta progress:', data);

            progressBar.style.width = `${data.percentage}%`;
            percentage.textContent = `${data.percentage}%`;
        }

    } catch (error) {
        console.log('Erro ao buscar progresso:', error);
    }
}
