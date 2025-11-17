<?php
$activePage = 'nguoi_cham_soc';
$pageTitle = 'Người Chăm Sóc';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?></title>
<style>
   /* ====== GIAO DIỆN TỔNG ====== */
                                        body {
                                            font-family: "Segoe UI", sans-serif;
                                            background-color: #f0f4f8;
                                            color: #333;
                                            margin: 0;
                                            padding: 0;
                                        }
                                        .container {
                                            display: flex;
                                            min-height: 100vh;
                                        }
                                        .main-content {
                                            flex-grow: 1;
                                            background: #fff;
                                            padding: 25px 40px;
                                            border-radius: 12px;
                                            margin: 20px;
                                            box-shadow: 0 0 10px rgba(0,0,0,0.05);
                                        }

                                        /* ====== THANH NAVBAR ====== */
                                        .navbar {
                                            display: flex;
                                            justify-content: space-between;
                                            align-items: center;
                                            border-bottom: 3px solid #3498db;
                                            padding-bottom: 15px;
                                            margin-bottom: 10px;
                                        }
                                        .navbar h1 {
                                            color: #3498db;
                                            font-size: 22px;
                                            font-weight: 600;
                                        }

                                        /* ====== THANH TÌM KIẾM ====== */
                                        .search input {
                                            padding: 7px 10px;
                                            border: 1px solid #ccc;
                                            border-radius: 6px;
                                            width: 260px;
                                        }
                                        .search button {
                                            background: #3498db;
                                            color: white;
                                            border: none;
                                            padding: 7px 12px;
                                            border-radius: 6px;
                                            cursor: pointer;
                                            transition: 0.3s;
                                        }
                                        .search button:hover {
                                            background: #2980b9;
                                        }

                                        /* ====== NÚT THÊM ====== */
                                        .add-btn {
                                            background-color: #2ecc71;
                                            color: white;
                                            padding: 8px 14px;
                                            border-radius: 6px;
                                            text-decoration: none;
                                            font-weight: 600;
                                            display: inline-block;
                                            margin-top: 15px;
                                            transition: 0.3s;
                                        }
                                        .add-btn:hover {
                                            background-color: #27ae60;
                                        }

                                        /* ====== BẢNG ====== */
                                        table {
                                            width: 100%;
                                            border-collapse: collapse;
                                            margin-top: 25px;
                                            background: #fff;
                                            border-radius: 10px;
                                            overflow: hidden;
                                            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                                        }
                                        th {
                                            background: #3498db;
                                            color: #fff;
                                            padding: 12px;
                                            font-weight: 600;
                                            text-transform: uppercase;
                                            font-size: 14px;
                                        }
                                        td {
                                            padding: 10px;
                                            border-bottom: 1px solid #eee;
                                            text-align: center;
                                            font-size: 15px;
                                        }
                                        tr:nth-child(even) {
                                            background: #f9f9f9;
                                        }
                                        tr:hover {
                                            background: #eaf4ff;
                                            transition: 0.2s;
                                        }

                                        /* ====== ẢNH ====== */
                                        img {
                                            width: 80px;
                                            height: 80px;
                                            border-radius: 8px;
                                            object-fit: cover;
                                            box-shadow: 0 0 5px rgba(0,0,0,0.1);
                                        }

                                        /* ====== NÚT XEM ĐÁNH GIÁ ====== */
                                        .view-btn {
                                            background: #f1c40f;
                                            color: #000;
                                            border: none;
                                            padding: 7px 12px;
                                            border-radius: 6px;
                                            cursor: pointer;
                                            font-weight: 600;
                                            transition: 0.3s;
                                        }
                                        .view-btn:hover {
                                            background: #d4ac0d;
                                            transform: scale(1.05);
                                        }

                                        /* ====== LIÊN KẾT HÀNH ĐỘNG ====== */
                                        .action-links a {
                                            text-decoration: none;
                                            color: #2980b9;
                                            margin: 0 5px;
                                            font-weight: 500;
                                            transition: 0.3s;
                                        }
                                        .action-links a:hover {
                                            color: #e74c3c;
                                        }

                                        /* ====== DÒNG CHI TIẾT ====== */
                                        .order-details-row {
                                            background: #f8f9fa;
                                            display: none;
                                        }
                                        .order-details-row table {
                                            width: 100%;
                                            border: 1px solid #ddd;
                                            border-radius: 8px;
                                            margin-top: 8px;
                                        }
                                        .order-details-row th {
                                            background: #6c757d;
                                            color: white;
                                            padding: 8px;
                                        }
                                        .order-details-row td {
                                            background: #fff;
                                            padding: 8px;
                                        }

                                        /* ====== SAO ====== */
                                        .star {
                                            color: #f1c40f;
                                            font-weight: bold;
                                        }
</style>
</head>
<body>
<div class="container">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <header class="navbar">
            <h1>Người Chăm Sóc</h1>
            <div class="search">
                <input type="text" id="searchInput" placeholder="Tìm kiếm người chăm sóc...">
                <button id="searchBtn">🔍</button>
            </div>
        </header>

        <a href="them_nguoi_cham_soc.php" class="add-btn">➕ Thêm Hồ Sơ</a>

        <table id="caregiverTable">
            <thead>
                <tr>
                    <th>Mã</th><th>Ảnh</th><th>Họ và tên</th><th>Địa chỉ</th>
                    <th>Tuổi</th><th>Giới tính</th><th>Chiều cao</th><th>Cân nặng</th>
                    <th>Đánh giá TB</th><th>Kinh nghiệm</th><th>Thao tác</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </main>
</div>

<script>
async function loadCaregivers(keyword=''){
    try{
        const res = await fetch(`../../backend/user/nguoi_cham_soc.php?keyword=${encodeURIComponent(keyword)}`);
        const data = await res.json();

        const tbody = document.querySelector('#caregiverTable tbody');
        tbody.innerHTML = '';

        if(!data.data || data.data.length===0){
            tbody.innerHTML = `<tr><td colspan="11" style="text-align:center;">Không có người chăm sóc nào.</td></tr>`;
            return;
        }

        data.data.forEach(c=>{
            const rating = c.danh_gia_tb>0 ? parseFloat(c.danh_gia_tb).toFixed(1)+'⭐' : '—';
            const imgTag = c.hinh_anh ? `<img src="../../${c.hinh_anh}" alt="Ảnh">` 
                                       : `<div style="width:80px;height:80px;background:#ddd;border-radius:8px;margin:0 auto;"></div>`;

            const row = document.createElement('tr');
            row.id = `row-${c.id_cham_soc}`;
            row.innerHTML = `
                <td>${c.id_cham_soc}</td>
                <td>${imgTag}</td>
                <td>${c.ho_ten}</td>
                <td>${c.dia_chi}</td>
                <td>${c.tuoi}</td>
                <td>${c.gioi_tinh}</td>
                <td>${c.chieu_cao}</td>
                <td>${c.can_nang}</td>
                <td>${rating}</td>
                <td>${c.kinh_nghiem}</td>
                <td class="action-links">
                    <button class="view-btn" data-id="${c.id_cham_soc}">👁 Xem đánh giá</button><br>
                    <a href="sua_nguoi_cham_soc.php?id=${c.id_cham_soc}">✏ Sửa</a> |
                    <span class="delete-btn" data-id="${c.id_cham_soc}">🗑 Xóa</span>
                </td>
            `;
            tbody.appendChild(row);

            // Chi tiết đánh giá
            const reviewRow = document.createElement('tr');
            reviewRow.classList.add('order-details-row');
            const reviewCell = document.createElement('td');
            reviewCell.colSpan = 11;
            let reviewTable = '<table><tr><th>Khách hàng</th><th>Số sao</th><th>Nhận xét</th><th>Ngày</th></tr>';
            if(c.reviews.length>0){
                c.reviews.forEach(r=>{
                    reviewTable += `<tr>
                        <td>${r.ten_khach_hang}</td>
                        <td class="star">${r.so_sao}⭐</td>
                        <td>${r.nhan_xet}</td>
                        <td>${r.ngay_danh_gia}</td>
                    </tr>`;
                });
            } else {
                reviewTable += '<tr><td colspan="4" style="text-align:center;">Chưa có đánh giá nào.</td></tr>';
            }
            reviewTable += '</table>';
            reviewCell.innerHTML = reviewTable;
            reviewRow.appendChild(reviewCell);
            tbody.appendChild(reviewRow);
        });

        // Xem đánh giá
        document.querySelectorAll('.view-btn').forEach(btn=>{
            btn.addEventListener('click', ()=>{
                const id = btn.dataset.id;
                document.querySelectorAll('#caregiverTable tbody .order-details-row').forEach(r=>{
                    if(r.previousElementSibling.querySelector('.view-btn').dataset.id===id){
                        r.style.display = (r.style.display==='table-row') ? 'none' : 'table-row';
                    }
                });
            });
        });

        // Xóa người chăm sóc
        document.querySelectorAll('.delete-btn').forEach(btn=>{
            btn.addEventListener('click', async ()=>{
                const id = btn.dataset.id;
                if(confirm("Bạn có chắc muốn xóa người chăm sóc này?")){
                    try{
                        const resDel = await fetch(`../../backend/user/xoa_nguoi_cham_soc.php?id=${id}`);
                        const dataDel = await resDel.json();
                        if(dataDel.status==='success'){
                            document.querySelector(`#row-${id}`).nextElementSibling.remove();
                            document.querySelector(`#row-${id}`).remove();
                            alert(dataDel.message);
                        } else alert(dataDel.message);
                    } catch(e){ console.error(e); alert('Xóa thất bại!'); }
                }
            });
        });

    } catch(e){
        console.error(e);
        alert('Lấy danh sách người chăm sóc thất bại!');
    }
}

loadCaregivers();

document.querySelector('#searchBtn').addEventListener('click', ()=>loadCaregivers(document.querySelector('#searchInput').value));
document.querySelector('#searchInput').addEventListener('keypress', e=>{
    if(e.key==='Enter') document.querySelector('#searchBtn').click();
});
</script>
</body>
</html>
