async function goalCompleted() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    const container = document.getElementById('result-finished');

    try {
        const response = await fetch('http://127.0.0.1:8000/api/goals/completed', {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (response.ok) {
            container.innerHTML = '';

            if (!data.goal || data.goal.length === 0) {
                container.innerHTML = '<p>Você ainda não concluiu nenhuma meta. Continue guardando!</p>';
                return;
            }

            data.goal.forEach(goal => {
                container.innerHTML += `
                    <div class="goal-card completed-card">
                        <div class="badge-completed">Concluído</div>
                        
                        <p>
                            <strong>Título:</strong> ${goal.title}<br>
                            <strong>Descrição:</strong> ${goal.description || 'Sem descrição'}<br>
                            <strong>Valor Atingido:</strong> R$ ${parseFloat(goal.target_amount).toFixed(2)}
                        </p>

                        <img 
                            src="http://127.0.0.1:8000/img/goals/${goal.image_path}" 
                            alt="${goal.title}"
                            style="width: 200px; height: auto;"
                        >

                        <div class="progress-container">
                            <div class="progress-bar" style="width: 100%; background-color: #2e7d32;"></div>
                        </div>

                        <p>Progresso: <strong>100%</strong></p>
                    </div>
                `;
            });

        } else {
            console.error('Erro ao carregar API:', data);
        }

    } catch (error) {
        console.error('Erro na requisição:', error);
    }
}

goalCompleted();