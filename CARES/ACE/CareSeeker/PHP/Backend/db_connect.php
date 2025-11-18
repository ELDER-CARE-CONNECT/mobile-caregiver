<?php
/**
 * Database Connection - Cấu hình cho XAMPP
 * File: db_connect.php
 */

// Cấu hình database cho XAMPP
$host = '127.0.0.1';      // Localhost cho XAMPP (không dùng host.docker.internal)
$dbname = 'sanpham';      // Tên database
$username = 'root';        // User mặc định XAMPP
$password = '';            // Password mặc định XAMPP là rỗng
$charset = 'utf8mb4';      // Charset UTF-8

// Data Source Name
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

// PDO Options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,    // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          // Fetch as associative array
    PDO::ATTR_EMULATE_PREPARES   => false,                     // Disable emulated prepares
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"        // Set charset
];

/**
 * Trả về một kết nối PDO
 * 
 * @return PDO
 * @throws PDOException Nếu không thể kết nối
 */
function get_pdo_connection() {
    global $dsn, $username, $password, $options;
    
    try {
        $pdo = new PDO($dsn, $username, $password, $options);
        
        // Log successful connection (chỉ trong development)
        if (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === true) {
            error_log("[DB] Connection established successfully");
        }
        
        return $pdo;
        
    } catch (PDOException $e) {
        // Log chi tiết lỗi vào error log
        error_log("[DB Connection Error] " . $e->getMessage());
        error_log("[DB Config] Host: $dsn, User: $username");
        
        // Throw exception với message thân thiện
        throw new PDOException(
            "Không thể kết nối database. " . 
            "Vui lòng kiểm tra: " .
            "1) MySQL đã chạy trên XAMPP chưa? " .
            "2) Database 'sanpham' đã tồn tại chưa? " .
            "3) User/password có đúng không? " .
            "Chi tiết: " . $e->getMessage()
        );
    }
}

/**
 * Test kết nối database (Dùng để debug)
 * 
 * @return bool True nếu kết nối thành công
 */
function test_database_connection() {
    try {
        $pdo = get_pdo_connection();
        $stmt = $pdo->query("SELECT 1");
        $result = $stmt->fetch();
        
        return $result !== false;
        
    } catch (PDOException $e) {
        error_log("[DB Test Failed] " . $e->getMessage());
        return false;
    }
}

// Nếu file này được gọi trực tiếp, test connection
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    echo "<h2>Database Connection Test</h2>";
    
    if (test_database_connection()) {
        echo "<p style='color: green; font-weight: bold;'>✅ Kết nối database thành công!</p>";
        
        try {
            $pdo = get_pdo_connection();
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<h3>Danh sách bảng trong database:</h3>";
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>" . htmlspecialchars($table) . "</li>";
            }
            echo "</ul>";
            
        } catch (PDOException $e) {
            echo "<p style='color: orange;'>⚠️ Kết nối OK nhưng không thể lấy danh sách bảng</p>";
        }
        
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Không thể kết nối database!</p>";
        echo "<p>Kiểm tra:</p>";
        echo "<ul>";
        echo "<li>XAMPP đã chạy chưa? (Apache + MySQL)</li>";
        echo "<li>Database 'sanpham' đã tồn tại chưa?</li>";
        echo "<li>Cấu hình host/user/password có đúng không?</li>";
        echo "</ul>";
    }
}
?>