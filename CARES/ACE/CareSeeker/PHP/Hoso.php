<<<<<<< HEAD
<<<<<<< HEAD

=======
<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sanpham";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ket noi that bai: " . $conn->connect_error);
}

$errors = [];
$success = "";
$profile = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten = trim($_POST['ho_ten'] ?? '');
    $dia_chi = trim($_POST['dia_chi'] ?? '');
    $so_dt = trim($_POST['so_dt'] ?? '');
    $tuoi = intval($_POST['tuoi'] ?? 0);
    $gioi_tinh = $_POST['gioi_tinh'] ?? '';
    $chieu_cao = floatval($_POST['chieu_cao'] ?? 0);
    $can_nang = floatval($_POST['can_nang'] ?? 0);

    if ($ho_ten === '') $errors[] = 'Vui lòng nhập họ và tên.';
    if ($so_dt === '') $errors[] = 'Vui lòng nhập số điện thoại.';
    if ($tuoi <= 0) $errors[] = 'Vui lòng nhập tuổi hợp lệ.';
    if (!in_array($gioi_tinh, ['Nam','Nu','Khac'])) $errors[] = 'Vui lòng chọn giới tính.';

    $uploadedFile = null;

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['avatar'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/png','image/jpeg','image/jpg','image/gif'];
            if (in_array($file['type'], $allowed) && $file['size'] <= 2 * 1024 * 1024) {
                $result = $conn->query("SELECT MAX(id_khach_hang) AS max_id FROM khach_hang");
                $row = $result->fetch_assoc();
                $nextId = ($row['max_id'] ?? 0) + 1;

                $uploadDir = __DIR__ . '/uploads';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = $nextId . '.' . strtolower($ext);
                $target = $uploadDir . '/' . $filename;

                if (move_uploaded_file($file['tmp_name'], $target)) {
                    $uploadedFile = 'uploads/' . $filename; 
                } else {
                    $errors[] = "Không thể lưu file ảnh.";
                }
            } else {
                $errors[] = "Ảnh không hợp lệ hoặc vượt quá 2MB.";
            }
        } else {
            $errors[] = "Lỗi khi tải ảnh lên.";
        }
    }

    if (empty($errors)) {
        $check = $conn->prepare("SELECT id_khach_hang FROM khach_hang WHERE so_dien_thoai = ?");
        $check->bind_param("s", $so_dt);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE khach_hang 
                SET ten_khach_hang=?, dia_chi=?, tuoi=?, gioi_tinh=?, chieu_cao=?, can_nang=?, hinh_anh=? 
                WHERE so_dien_thoai=?");
            $stmt->bind_param("sssdssds", $ho_ten, $dia_chi, $tuoi, $gioi_tinh, $chieu_cao, $can_nang, $uploadedFile, $so_dt);
            if ($stmt->execute()) {
                $success = "Cập nhật hồ sơ thành công!";
            } else {
                $errors[] = "Lỗi khi cập nhật: " . $stmt->error;
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO khach_hang 
                (ten_khach_hang, so_dien_thoai, dia_chi, tuoi, gioi_tinh, chieu_cao, can_nang, hinh_anh, mat_khau, role) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, '', 0)");
            $stmt->bind_param("sssssdss", $ho_ten, $so_dt, $dia_chi, $tuoi, $gioi_tinh, $chieu_cao, $can_nang, $uploadedFile);
            if ($stmt->execute()) {
                $success = "Lưu hồ sơ mới thành công!";
            } else {
                $errors[] = "Lỗi khi lưu mới: " . $stmt->error;
            }
        }

        $stmt->close();
        $check->close();
        $profile = [
            'ho_ten' => $ho_ten,
            'dia_chi' => $dia_chi,
            'so_dt' => $so_dt,
            'tuoi' => $tuoi,
            'gioi_tinh' => $gioi_tinh,
            'chieu_cao' => $chieu_cao,
            'can_nang' => $can_nang,
            'avatar' => $uploadedFile,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $_SESSION['profile'] = $profile;
    }
}

$conn->close();
?>

<!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tao ho so - Demo</title>
<style>
:root{--accent:#ff6b81;--muted:#666}
*{box-sizing:border-box}
body{font-family:Inter, system-ui, Arial, sans-serif;background:linear-gradient(180deg,#f7f3ff 0%,#fff 100%);margin:0;padding:24px;color:#222}
.container{max-width:920px;margin:0 auto;background:#fff;border-radius:12px;box-shadow:0 8px 30px rgba(15,15,15,0.06);overflow:hidden;display:grid;grid-template-columns:1fr 420px}
.form-wrap{padding:28px}
.form-wrap h2{margin:0 0 12px;font-size:20px}
.field{margin-bottom:12px}
label{display:block;font-size:13px;margin-bottom:6px;color:var(--muted)}
input[type="text"], input[type="number"], input[type="file"], select, textarea{width:100%;padding:10px;border:1px solid #e6e6e6;border-radius:8px;font-size:14px}
.row{display:flex;gap:12px}
.row .col{flex:1}
.btn{background:var(--accent);color:#fff;padding:10px 14px;border:none;border-radius:10px;cursor:pointer;font-weight:600}
.btn:active{transform:translateY(1px)}
.preview{padding:24px;background:linear-gradient(180deg,#fcfcff,#f9fbff);border-left:1px solid #f0f0f5}
.preview h3{margin-top:0}
.avatar-preview{width:180px;height:180px;border-radius:12px;background:#fff;border:1px dashed #ddd;display:flex;align-items:center;justify-content:center;overflow:hidden}
.avatar-preview img{width:100%;height:100%;object-fit:cover}
.small{font-size:13px;color:var(--muted)}
.errors{background:#fff4f4;border:1px solid #ffd2d2;padding:10px;border-radius:8px;margin-bottom:12px;color:#9b1c1c}
.success{background:#f2fff4;border:1px solid #b8efc1;padding:10px;border-radius:8px;margin-bottom:12px;color:#2a7a2a}
.field-inline{display:flex;gap:8px;align-items:center}
.footer{padding:12px;text-align:center;color:#888;font-size:13px}
@media(max-width:880px){.container{grid-template-columns:1fr;}.preview{border-left:none;border-top:1px solid #f0f0f5}}
</style>
</head>
<body>
<div class="container">
  <div class="form-wrap">
    <h2>Tạo hồ sơ cá nhân</h2>

    <?php if (!empty($errors)): ?>
      <div class="errors">
        <ul>
        <?php foreach($errors as $e): ?>
          <li><?php echo htmlspecialchars($e); ?></li>
        <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($profile && empty($errors)): ?>
      <div class="success">Hồ sơ được lưu thành công vào <?php echo $profile['created_at']; ?>.</div>
    <?php endif; ?>

    <form id="profileForm" method="post" enctype="multipart/form-data" novalidate>
      <div class="field">
        <label for="avatar">Hình ảnh cá nhân</label>
        <input type="file" id="avatar" name="avatar" accept="image/*">
      </div>

      <div class="field">
        <label for="ho_ten">Họ và tên</label>
        <input type="text" id="ho_ten" name="ho_ten" value="<?php echo isset($profile['ho_ten']) ? $profile['ho_ten'] : ''; ?>" required>
      </div>

      <div class="field">
        <label for="dia_chi">Địa chỉ</label>
        <input type="text" id="dia_chi" name="dia_chi" value="<?php echo isset($profile['dia_chi']) ? $profile['dia_chi'] : ''; ?>">
      </div>

      <div class="row">
        <div class="col field">
          <label for="so_dt">Số điện thọai</label>
          <input type="text" id="so_dt" name="so_dt" value="<?php echo isset($profile['so_dt']) ? $profile['so_dt'] : ''; ?>" required>
        </div>
        <div class="col field">
          <label for="tuoi">Tuổi</label>
          <input type="number" id="tuoi" name="tuoi" min="1" value="<?php echo isset($profile['tuoi']) ? $profile['tuoi'] : ''; ?>" required>
        </div>
      </div>

      <div class="field">
        <label>Giói tính</label>
        <div class="field-inline">
          <label><input type="radio" name="gioi_tinh" value="Nam" <?php echo (isset($profile['gioi_tinh']) && $profile['gioi_tinh']==='Nam')? 'checked':''; ?>> Nam</label>
          <label><input type="radio" name="gioi_tinh" value="Nữ" <?php echo (isset($profile['gioi_tinh']) && $profile['gioi_tinh']==='Nu')? 'checked':''; ?>> Nữ</label>
          <label><input type="radio" name="gioi_tinh" value="Khac" <?php echo (isset($profile['gioi_tinh']) && $profile['gioi_tinh']==='Khac')? 'checked':''; ?>> Khac</label>
        </div>
      </div>

      <div class="row">
        <div class="col field">
          <label for="chieu_cao">Chiều cao (cm)</label>
          <input type="number" id="chieu_cao" name="chieu_cao" min="0" step="0.1" value="<?php echo isset($profile['chieu_cao']) ? $profile['chieu_cao'] : ''; ?>">
        </div>
        <div class="col field">
          <label for="can_nang">Cân nặng (kg)</label>
          <input type="number" id="can_nang" name="can_nang" min="0" step="0.1" value="<?php echo isset($profile['can_nang']) ? $profile['can_nang'] : ''; ?>">
        </div>
      </div>

      <div style="display:flex;gap:8px;margin-top:14px">
        <button type="submit" class="btn">Lưu hồ sơ</button>
        <button type="button" id="resetBtn" class="btn" style="background:#ddd;color:#222">Đặt lại</button>
      </div>
    </form>

  </div>

  <div class="preview">
    <h3>Xem trước hồ sơ</h3>
    <div style="display:flex;gap:16px;align-items:flex-start">
      <div class="avatar-preview" id="avatarBox">
        <?php if (isset($profile['avatar']) && $profile['avatar']): ?>
          <img src="<?php echo $profile['avatar']; ?>" alt="avatar">
        <?php else: ?>
          <div class="small">Chưa có ảnh</div>
        <?php endif; ?>
      </div>
      <div>
        <div style="margin-bottom:10px"><strong><?php echo $profile['ho_ten'] ?? '---'; ?></strong></div>
        <div class="small">Địa chỉ: <?php echo $profile['dia_chi'] ?? '---'; ?></div>
        <div class="small">SĐT: <?php echo $profile['so_dt'] ?? '---'; ?></div>
        <div class="small">Tuổi: <?php echo $profile['tuoi'] ?? '---'; ?></div>
        <div class="small">Giới tính: <?php echo $profile['gioi_tinh'] ?? '---'; ?></div>
        <div class="small">Chiều cao: <?php echo ($profile['chieu_cao'] ?? '---'); ?> cm</div>
        <div class="small">Cân nặng: <?php echo ($profile['can_nang'] ?? '---'); ?> kg</div>
      </div>
    </div>
   
  </div>
</div>

<script>
const avatarInput = document.getElementById('avatar');
const avatarBox = document.getElementById('avatarBox');
const form = document.getElementById('profileForm');
const resetBtn = document.getElementById('resetBtn');

avatarInput && avatarInput.addEventListener('change', function(e){
  const file = e.target.files[0];
  if (!file) return;
  if (!file.type.startsWith('image/')) return;
  const reader = new FileReader();
  reader.onload = function(ev){
    avatarBox.innerHTML = '';
    const img = document.createElement('img');
    img.src = ev.target.result;
    avatarBox.appendChild(img);
  }
  reader.readAsDataURL(file);
});

resetBtn.addEventListener('click', function(){
  form.reset();
  avatarBox.innerHTML = '<div class="small">Chưa có ảnh</div>';
});

form.addEventListener('submit', function(e){
  const ho_ten = document.getElementById('ho_ten').value.trim();
  const so_dt = document.getElementById('so_dt').value.trim();
  const tuoi = Number(document.getElementById('tuoi').value);
  let msgs = [];
  if (!ho_ten) msgs.push('Vui lòng nhập họ và tên.');
  if (!so_dt) msgs.push('Vui lòng nhập số điện thọai.');
  if (!tuoi || tuoi <= 0) msgs.push('Vui lòng nhập tuổi hợp lệ.');
  if (msgs.length){
    e.preventDefault();
    alert(msgs.join('\n'));
    return false;
  }
});
</script>
</body>
</html>
>>>>>>> Trí
=======

>>>>>>> Phong
