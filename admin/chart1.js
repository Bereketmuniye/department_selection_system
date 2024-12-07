var ctx = document.getElementById('barChart');
if (ctx && typeof userRoleData !== 'undefined') {
    var labels = userRoleData.map(function(roleData) {
        return roleData.role;
    });
    var dataCounts = userRoleData.map(function(roleData) {
        return roleData.count;
    });

    var myChart = new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Number of Users by Role',
                data: dataCounts,
                backgroundColor: 'rgba(41, 155, 99, 0.6)',
                borderColor: 'rgba(41, 155, 99, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
}
