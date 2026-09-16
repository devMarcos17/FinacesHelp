async function getUsersTotal() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    try {
        const response = await fetch('http://127.0.0.1:8000/api/auth/usersTotal', {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json',
            },
        });
        const data = await response.json();
        const totalUsersElement = document.getElementById('total-users');
        if (totalUsersElement) {
            totalUsersElement.textContent = data.users  ;
        }
    } catch (error) {
        console.log(error);
    }
}
getUsersTotal();
async function getAdmnisTotal() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    try {
        const response = await fetch('http://127.0.0.1:8000/api/auth/adminsTotal', {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json',
            },
        });
        const data = await response.json();
        const totalUsersElement = document.getElementById('admin-users');
        if (totalUsersElement) {
            totalUsersElement.textContent = data.users  ;
        }
    } catch (error) {
        console.log(error);
    }
}
getAdmnisTotal();
async function getActiveUserTotal() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    try {
        const response = await fetch('http://127.0.0.1:8000/api/auth/activeUsersTotal', {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json',
            },
        });
        const data = await response.json();
        const totalUsersElement = document.getElementById('active-users');
        if (totalUsersElement) {
            totalUsersElement.textContent = data.users  ;
        }
    } catch (error) {
        console.log(error);
    }
}
getActiveUserTotal();
async function getDisableUsersTotal() {
    const token = 'Bearer ' + sessionStorage.getItem('session');
    try {
        const response = await fetch('http://127.0.0.1:8000/api/auth/disableUsersTotal', {
            method: 'GET',
            headers: {
                'Authorization': token,
                'Accept': 'application/json',
            },
        });
        const data = await response.json();
        const totalUsersElement = document.getElementById('inactive-users');
        if (totalUsersElement) {
            totalUsersElement.textContent = data.users  ;
        }
    } catch (error) {
        console.log(error);
    }
}
getDisableUsersTotal();