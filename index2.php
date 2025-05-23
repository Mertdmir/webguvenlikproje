<?php
session_start();

// Veritabanı bağlantısı
$conn = new mysqli("localhost", "root", "", "guvenlikdb");
if ($conn->connect_error) {
    die("Veritabanı bağlantı hatası: " . $conn->connect_error);
}

// Güvensiz session_id ayarlama kaldırıldı

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Güvenli sorgu (prepared statement)
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Şifre doğrulama (şifreler veritabanında hash'li olmalı)
        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true); // Session Fixation koruması
            $_SESSION['user'] = $username;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Kullanıcı adı veya şifre hatalı!";
        }
    } else {
        $error = "Kullanıcı adı veya şifre hatalı!";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Giriş Yap</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f2f2f2;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .login-container {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      width: 300px;
    }
    h2 {
      text-align: center;
      color: #007BFF;
    }
    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    input[type="submit"] {
      width: 100%;
      margin-top: 20px;
      padding: 10px;
      background-color: #007BFF;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .error {
      color: red;
      margin-top: 10px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Login</h2>
    <?php if (isset($error)) echo "<div class='error'>" . htmlspecialchars($error) . "</div>"; ?>
    <form method="post">
      <input type="text" name="username" placeholder="Kullanıcı Adı" required>
      <input type="password" name="password" placeholder="Şifre" required>
      <input type="submit" name="login" value="Giriş Yap">
    </form>
  </div>
</body>
</html>
