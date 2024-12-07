var ctx2 = document.getElementById('doughnut');
if (ctx2 && typeof departmentCategoryData !== 'undefined') {
    var labels = departmentCategoryData.map(function(categoryData) {
        return categoryData.category;
    });
    var dataCounts = departmentCategoryData.map(function(categoryData) {
        return categoryData.count;
    });

    var myChart2 = new Chart(ctx2.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: 'Departments by Category',
                data: dataCounts,
                backgroundColor: [
                    'rgba(41, 155, 99, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 99, 132, 1)' // Add more colors as needed
                ],
                borderColor: [
                    'rgba(41, 155, 99, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 99, 132, 1)' // Match colors for borders
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
}
