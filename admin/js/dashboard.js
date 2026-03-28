document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        document.querySelector('.sidebar').classList.toggle('collapsed');
    });

    $(document).ready(function () {
        $('#applicationsTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "lengthChange": false,
            "pageLength": 5
        });

        $('.filter-status').on('click', function (e) {
            e.preventDefault();
            var status = $(this).data('status');

            if (status === 'all') {
                $('tr[data-status]').show();
            } else {
                $('tr[data-status]').hide();
                $('tr[data-status="' + status + '"]').show();
            }
        });

        $('.approve-btn').on('click', function () {
            var id = $(this).data('id');
            updateApplicationStatus(id, 'Approved');
        });

        $('.deny-btn').on('click', function () {
            var id = $(this).data('id');
            updateApplicationStatus(id, 'Denied');
        });

        function updateApplicationStatus(id, status) {
            $('#status-' + id).text(status);
            $('#status-' + id).removeClass('bg-warning bg-success bg-danger');

            if (status === 'Approved') {
                $('#status-' + id).addClass('bg-success');
                $('#row-' + id + ' .approve-btn, #row-' + id + ' .deny-btn').hide();
            } else if (status === 'Denied') {
                $('#status-' + id).addClass('bg-danger');
                $('#row-' + id + ' .approve-btn, #row-' + id + ' .deny-btn').hide();
            }

            alert('Application #' + id + ' has been ' + status.toLowerCase());
        }

        $('[data-image]').on('click', function() {
            var imageSrc = $(this).data('image');
            $('#modalImage').attr('src', imageSrc);
            $('#imageModal').modal('show');
        });

        if (document.getElementById('applicationsChart')) {
            const trendCtx = document.getElementById('applicationsChart').getContext('2d');
            const trendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: dashboardData.chartData.map(item => item.month),
                    datasets: [
                        {
                            label: 'Total Applications',
                            data: dashboardData.chartData.map(item => item.total_apps),
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Approved',
                            data: dashboardData.chartData.map(item => item.approved_apps),
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Pending',
                            data: dashboardData.chartData.map(item => item.pending_apps),
                            borderColor: 'rgba(255, 206, 86, 1)',
                            backgroundColor: 'rgba(255, 206, 86, 0.2)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        if (document.getElementById('applicationStatusChart')) {
            const statusCtx = document.getElementById('applicationStatusChart').getContext('2d');
            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Approved', 'Pending', 'Denied'],
                    datasets: [{
                        data: [
                            dashboardData.approvedApplications,
                            dashboardData.pendingApplications,
                            dashboardData.totalApplications - dashboardData.approvedApplications - dashboardData.pendingApplications
                        ],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(255, 99, 132, 0.8)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    });
});