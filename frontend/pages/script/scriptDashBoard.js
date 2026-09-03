async function getDashBoard()
{
    const token = 'Bearer ' + sessionStorage.getItem('session');
    const response = await fetch('http://127.0.0.1:8000/api/dashboard/dashboard',{
        method:'get',
        headers:{
            'Authorization':token,
            'Accept': 'application/json',
        }
    });
    const data = await response.json();
    if(response){
        console.log(data.summary.balance);
        console.log(data.revenue);
        console.log(data.expense);
        data.expensesByCategory.forEach(item => {
    console.log(item.category);
    console.log(item.total);
});
    }
}
getDashBoard();