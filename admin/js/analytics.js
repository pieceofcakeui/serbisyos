document.addEventListener("DOMContentLoaded", function () {
    console.log("Total Shops:", dashboardData.totalShops);
    console.log("Approved Shops:", dashboardData.approvedShops);
    console.log("Pending Shops:", dashboardData.pendingShops);
    console.log("Rejected Shops:", dashboardData.rejectedShops);

    let userCtx = document.getElementById("userChart").getContext("2d");
    new Chart(userCtx, {
        type: "doughnut",
        data: {
            labels: ["Owners", "Regular Users"],
            datasets: [{
                data: [dashboardData.totalOwners, dashboardData.totalRegularUsers],
                backgroundColor: ["#28a745", "#007bff"],
            }],
        },
        options: { responsive: true }
    });

    let shopCanvas = document.getElementById("shopChart");
    if (!shopCanvas) {
        console.error("shopChart canvas not found!");
        return;
    }

    let shopCtx = shopCanvas.getContext("2d");
    new Chart(shopCtx, {
        type: "bar",
        data: {
            labels: ["Total Shops", "Approved", "Pending", "Rejected"],
            datasets: [{
                label: "Shop Status",
                data: [
                    dashboardData.totalShops,
                    dashboardData.approvedShops,
                    dashboardData.pendingShops,
                    dashboardData.rejectedShops
                ],
                backgroundColor: ["#007bff", "#28a745", "#ffc107", "#dc3545"],
            }],
        },
        options: { responsive: true }
    });
});
