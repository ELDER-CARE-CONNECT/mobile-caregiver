<?php
// File: Backend/api_rating.php

// 1. Dọn sạch buffer
if (ob_get_length()) ob_clean();

global $conn;

// 2. Kiểm tra Auth
if (!isset($_SESSION['id_khach_hang'])) {
    sendResponse(401, ['success' => false, 'message' => 'Vui lòng đăng nhập để đánh giá.']);
}

$id_khach_hang = $_SESSION['id_khach_hang']; 

// 3. Lấy dữ liệu
$input = $GLOBALS['api_input'];
$action = $input['action'] ?? $_POST['action'] ?? '';

if ($action === 'submit_rating') {
    $id_cham_soc = intval($input['id_cs'] ?? $_POST['id_cs'] ?? 0);
    $id_don_hang = intval($input['id_dh'] ?? $_POST['id_dh'] ?? 0);
    $rating      = intval($input['rating'] ?? $_POST['rating'] ?? 0);
    $comment     = trim($input['comment'] ?? $_POST['comment'] ?? '');

    // Validate
    if ($id_cham_soc <= 0 || $id_don_hang <= 0) {
        sendResponse(400, ['success' => false, 'message' => 'Thông tin đơn hàng không hợp lệ.']);
    }

    if ($rating < 1 || $rating > 5) {
        sendResponse(400, ['success' => false, 'message' => 'Vui lòng chọn số sao (1-5).']);
    }

    // 4. Kiểm tra quyền đánh giá (Đơn hàng phải HOÀN THÀNH và CHƯA ĐÁNH GIÁ)
    // Kiểm tra cột id_danh_gia trong bảng don_hang xem đã có chưa
    $check_sql = "SELECT id_danh_gia FROM don_hang 
                  WHERE id_don_hang = ? 
                  AND id_khach_hang = ? 
                  AND id_nguoi_cham_soc = ? 
                  AND trang_thai = 'đã hoàn thành'";
    
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("iii", $id_don_hang, $id_khach_hang, $id_cham_soc);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row_check = $result_check->fetch_assoc();
    $stmt_check->close();

    if (!$row_check) {
        sendResponse(403, ['success' => false, 'message' => 'Không thể đánh giá (Đơn chưa hoàn thành hoặc không tồn tại).']);
    }

    if ($row_check['id_danh_gia'] > 0) {
        sendResponse(409, ['success' => false, 'message' => 'Bạn đã đánh giá đơn hàng này rồi.']);
    }

    // 5. Thêm đánh giá (ĐÃ SỬA: BỎ CỘT id_don_hang)
    $sql_insert = "INSERT INTO danh_gia (id_khach_hang, id_cham_soc, so_sao, nhan_xet, ngay_danh_gia) 
                   VALUES (?, ?, ?, ?, NOW())";
    
    $stmt_insert = $conn->prepare($sql_insert);
    // Chỉ bind 4 tham số: id_khach_hang, id_cham_soc, rating, comment
    $stmt_insert->bind_param("iiis", $id_khach_hang, $id_cham_soc, $rating, $comment);

    if ($stmt_insert->execute()) {
        $new_review_id = $conn->insert_id; // Lấy ID đánh giá vừa tạo

        // 6. Cập nhật điểm trung bình
        $sql_avg = "UPDATE nguoi_cham_soc 
                    SET danh_gia_tb = (SELECT AVG(so_sao) FROM danh_gia WHERE id_cham_soc = ?) 
                    WHERE id_cham_soc = ?";
        $stmt_avg = $conn->prepare($sql_avg);
        $stmt_avg->bind_param("ii", $id_cham_soc, $id_cham_soc);
        $stmt_avg->execute();
        $stmt_avg->close();

        // 7. Cập nhật ID đánh giá vào bảng đơn hàng (Để đánh dấu đơn này đã xong)
        $update_don = "UPDATE don_hang SET id_danh_gia = ? WHERE id_don_hang = ?";
        $stmt_ud = $conn->prepare($update_don);
        $stmt_ud->bind_param("ii", $new_review_id, $id_don_hang);
        $stmt_ud->execute();
        $stmt_ud->close();

        sendResponse(200, ['success' => true, 'message' => 'Đánh giá thành công!']);
    } else {
        sendResponse(500, ['success' => false, 'message' => 'Lỗi hệ thống: ' . $conn->error]);
    }
    $stmt_insert->close();

} else {
    sendResponse(400, ['success' => false, 'message' => 'Hành động không hợp lệ.']);
}
?>