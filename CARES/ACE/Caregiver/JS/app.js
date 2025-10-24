// Dữ liệu từ database
let duLieuDonHang = [];
let tongSoTrang = 1;
let tongSoDon = 0;
let tongTien = 0;
  
  // Cấu hình phân trang
  const kichThuocTrang = 8;
  let trangHienTai = 1;
  
  const $ = (sel) => document.querySelector(sel);
  const $$ = (sel) => document.querySelectorAll(sel);
  
  const oDongMau = $("#dongMau");
  const bang = $("#noiDungBang");
  
  const elTim = $("#timKiem");
  const elTrangThai = $("#trangThai");
  const elTuNgay = $("#tuNgay");
  const elDenNgay = $("#denNgay");
  const elLamMoi = $("#lamMoi");
  
  const elTongDon = $("#tongDon");
  const elTongTien = $("#tongTien");
  
  const elTruoc = $("#truoc");
  const elSau = $("#sau");
  const elThongTinTrang = $("#thongTinTrang");
  
  // Định dạng tiền VND
  const dinhDangVND = (n) =>
    n.toLocaleString("vi-VN", { style: "currency", currency: "VND", maximumFractionDigits: 0 });
  
  // Hàm gọi API để lấy dữ liệu
  async function taiDuLieu() {
    try {
      const params = new URLSearchParams({
        search: elTim.value.trim(),
        status: elTrangThai.value,
        from_date: elTuNgay.value,
        to_date: elDenNgay.value,
        page: trangHienTai,
        limit: kichThuocTrang
      });
      
      const response = await fetch(`get_orders.php?${params}`);
      const data = await response.json();
      
      if (data.success) {
        duLieuDonHang = data.data;
        tongSoTrang = data.pagination.total_pages;
        tongSoDon = data.summary.total_orders;
        tongTien = data.summary.total_amount;
        renderBang();
      } else {
        console.error('Lỗi:', data.error);
        alert('Có lỗi xảy ra khi tải dữ liệu: ' + data.error);
      }
    } catch (error) {
      console.error('Lỗi kết nối:', error);
      alert('Không thể kết nối đến server');
    }
  }
  
  function capNhatThongKe() {
    elTongDon.textContent = tongSoDon;
    elTongTien.textContent = dinhDangVND(tongTien);
  }
  
  function renderBang() {
    capNhatThongKe();
  
    bang.innerHTML = "";
    duLieuDonHang.forEach(d => {
      const dong = oDongMau.content.firstElementChild.cloneNode(true);
      dong.querySelector(".ma-don").textContent = d.ma;
      dong.querySelector(".thoi-gian").textContent = `${d.ngay} ${d.gio}`;
      dong.querySelector(".khach-hang").textContent = d.khach_ten;
      dong.querySelector(".nguoi-cham-soc").textContent = d.nguoi_cham_soc;
      dong.querySelector(".gia-tien").textContent = dinhDangVND(d.gia);
      dong.querySelector(".thanh-toan").textContent = d.thanh_toan;
  
      const nhan = dong.querySelector(".nhan");
      nhan.textContent =
        d.trang_thai === "hoan_thanh" ? "Hoàn thành" :
        d.trang_thai === "da_huy" ? "Đã hủy" : "Đang xử lý";
      nhan.classList.add(`nhan--${d.trang_thai}`);
  
      // Hành động (demo)
      dong.querySelector(".xem-chi-tiet").addEventListener("click", () => {
        alert(`Xem chi tiết ${d.ma}`);
      });
      dong.querySelector(".dat-lai").addEventListener("click", () => {
        alert(`Đặt lại đơn ${d.ma}`);
      });
  
      bang.appendChild(dong);
    });
  
    elThongTinTrang.textContent = `Trang ${trangHienTai}/${tongSoTrang}`;
    elTruoc.disabled = trangHienTai === 1;
    elSau.disabled = trangHienTai === tongSoTrang;
  }
  
  elTim.addEventListener("input", () => { trangHienTai = 1; taiDuLieu(); });
  elTrangThai.addEventListener("change", () => { trangHienTai = 1; taiDuLieu(); });
  elTuNgay.addEventListener("change", () => { trangHienTai = 1; taiDuLieu(); });
  elDenNgay.addEventListener("change", () => { trangHienTai = 1; taiDuLieu(); });
  elLamMoi.addEventListener("click", () => {
    elTim.value = "";
    elTrangThai.value = "";
    elTuNgay.value = "";
    elDenNgay.value = "";
    trangHienTai = 1;
    taiDuLieu();
  });
  
  elTruoc.addEventListener("click", () => { if (trangHienTai > 1) { trangHienTai--; taiDuLieu(); } });
  elSau.addEventListener("click", () => { trangHienTai++; taiDuLieu(); });
  
  // Tải dữ liệu khi trang load
  taiDuLieu();
