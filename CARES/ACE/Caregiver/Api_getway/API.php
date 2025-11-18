<?php
header('Content-Type: application/json');
session_start();

// Lấy tham số
$dichvu = $_GET['dichvu'] ?? '';
$hanhdong = $_GET['hanhdong'] ?? '';

switch($dichvu){
    // Nếu cần các dịch vụ khác về DonHang
    case 'donhang':
        switch($hanhdong){
            case 'lay-nguoichamsoc':
                include_once('../Backend/DonHang/LayNguoiChamSoc.php');
                break;
            case 'lay-danhsach':
                include_once('../Backend/DonHang/LayDanhSachDon.php');
                break;
            case 'capnhat':
                include_once('../Backend/DonHang/CapNhatDon.php');
                break;
            default:
                echo json_encode(['status'=>'error','message'=>'Hành động không hợp lệ cho dịch vụ donhang']);
        }
        break;


    default:
        echo json_encode(['status'=>'error','message'=>'Dịch vụ không hợp lệ']);
        break;
}
