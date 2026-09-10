async function listInvestiment() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    const resultado = document.getElementById('resultado');

    if (!resultado) {
        return;
    }

    try {
        const response = await fetch(
            'http://127.0.0.1:8000/api/investiment/list',
            {
                method: 'GET',
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json'
                }
            }
        );

        const data = await response.json();

        if (response.ok) {
            resultado.innerHTML = '';

            data.investiments.forEach(investiment => {
                resultado.innerHTML += `
                    <div>
                        <p>
                            <strong>Título:</strong>
                            ${investiment.title}
                        </p>

                        <p>
                            <strong>Valor Investido:</strong>
                            R$ ${parseFloat(investiment.amount_invested).toFixed(2)}
                        </p>

                        <p>
                            <strong>Valor Atual:</strong>
                            R$ ${parseFloat(investiment.current_amount).toFixed(2)}
                        </p>

                        <p>
                            <strong>Rentabilidade:</strong>
                            <span id="profitability-${investiment.id}">
                                Carregando...
                            </span>
                        </p>

                        <canvas id="donut-${investiment.id}"></canvas>

                        <button
                            type="button"
                            onclick="deleteInvestiment(${investiment.id})"
                        >
                            Excluir
                        </button>

                        <button
                            type="button"
                            onclick="window.location.href='updateInvestiment.html?id=${investiment.id}'"
                        >
                            Atualizar
                        </button>

                        <hr>
                    </div>
                `;

                getProfitability(investiment.id);
            });

            // Cria os gráficos somente depois que todos os investimentos
            // já foram adicionados ao DOM.
            data.investiments.forEach(investiment => {
                createDonutChart(investiment);
            });
        }

    } catch (error) {
        console.log('Error:', error);
    }
}


async function getProfitability(id) {
    const token = 'Bearer ' + sessionStorage.getItem('session');

    try {
        const response = await fetch(
            'http://127.0.0.1:8000/api/investiment/profitability',
            {
                method: 'POST',
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: id })
            }
        );

        const data = await response.json();

        if (response.ok) {
            const span = document.getElementById(`profitability-${id}`);

            if (span) {
                span.textContent = `${data.profitability}%`;
            }
        }

    } catch (error) {
        console.log('Error:' + error);
    }
}


function createDonutChart(investiment) {
    const canvas = document.getElementById(`donut-${investiment.id}`);

    if (!canvas) {
        return;
    }

    const amountInvested = parseFloat(investiment.amount_invested);
    const currentAmount = parseFloat(investiment.current_amount);

    const profit = currentAmount - amountInvested;

    new Chart(canvas, {
        type: 'doughnut',

        data: {
            labels: [
                'Valor Investido',
                'Rendimento'
            ],

            datasets: [{
                data: [
                    amountInvested,
                    profit > 0 ? profit : 0
                ],

                backgroundColor: [
                    '#3498db',
                    '#2ecc71'
                ],

                borderWidth: 0
            }]
        },

        options: {
            responsive: true,

            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}


listInvestiment();


async function createInvestiment() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    const form = document.getElementById('formInvestiment');

    if (!form) {
        return;
    }

    form.addEventListener('submit', async event => {
        event.preventDefault();

        const formData = new FormData(form);

        try {
            const response = await fetch(
                'http://127.0.0.1:8000/api/investiment/create',
                {
                    method: 'POST',
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    },
                    body: formData
                }
            );

            const data = await response.json();

            if (response.ok) {
                alert('Investimento criado com sucesso!');
            } else {
                alert('Falha ao criar investimento');
            }

            console.log(data);

        } catch (error) {
            console.log(error);
        }
    });
}


createInvestiment();
