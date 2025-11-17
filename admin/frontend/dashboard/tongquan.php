<?php
$activePage = 'tongquan'; // Active menu
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tổng Quan</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
/* === Cards === */
.cards { display:flex; gap:20px; flex-wrap:nowrap; overflow-x:auto; margin-bottom:30px; }
.card { flex:1; min-width:180px; padding:20px; border-radius:12px; background:#fff; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s;}
.card:hover { transform: translateY(-5px);}
.card h3 { font-size:18px; color:#555; margin-bottom:10px; }
.card p { font-size:20px; font-weight:bold; color:#2c3e50; }
.main-content { padding:30px; margin-left:250px; } /* margin-left = sidebar width */
h1 { margin-bottom:30px; }

/* === Responsive === */
@media(max-width:900px){
  .main-content { margin-left:0; padding:20px; }
}
</style>
</head>
<body>
<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header><h1>Trang Tổng Quan</h1></header>

        <section class="stats">
            <div class="cards" id="cards-container">
                <!-- Cards sẽ render JS -->
            </div>
        </section>

        <h2>Biểu đồ doanh thu theo tháng</h2>
        <canvas id="revenueChart" width="400" height="200"></canvas>
    </main>
</div>

<script>
async function fetchDashboard(){
    try {
        const res = await fetch('../../backend/dashboard/tongquan.php');
        const data = await res.json();

        // --- Render cards ---
        const cardsContainer = document.getElementById('cards-container');
        cardsContainer.innerHTML = `
            <div class="card"><h3>Tổng Doanh Thu</h3><p>${Number(data.total_revenue).toLocaleString()} VND</p></div>
            <div class="card"><h3>Tổng Đơn Hàng</h3><p>${data.total_orders} đơn</p></div>
            <div class="card"><h3>Tổng Khách Hàng</h3><p>${data.total_customers} khách</p></div>
            <div class="card"><h3>Tổng Người Chăm Sóc</h3><p>${data.total_caregivers} người</p></div>
            <div class="card"><h3>Trung Bình Đánh Giá</h3><p>${data.avg_rating} ⭐</p></div>
        `;

        // --- Biểu đồ doanh thu ---
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Doanh thu (VND)',
                    data: data.data,
                    backgroundColor: 'rgba(52, 152, 219, 0.6)',
                    borderColor: 'rgba(41, 128, 185, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive:true,
                scales: {
                    y: { beginAtZero:true, ticks:{ callback: v=> '₫'+Number(v).toLocaleString() } }
                }
            }
        });

    } catch(err) {
        console.error("Lỗi fetch dashboard:", err);
        alert("Không thể tải dữ liệu dashboard.");
    }
}

fetchDashboard();
</script>
</body>
</html>
