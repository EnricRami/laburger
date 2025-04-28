<?php
session_start();
require 'dp.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if ($pass === $row['password']) {
            $_SESSION['admin_id'] = $row['id'];
            header('Location: admin_dashboard.php');
            exit;
        } else {
            $message = "Password incorrect.";
        }
    } else {
        $message = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - La Burger</title>
  <style>
    :root {
      --special-color: #AC1600;
      --main-bg: #FCFCE4;
      --input-border: #f6ab83;
      --btn-bg: #AC1600;
      --btn-color: #fff;
      --error-color: #d63031;
    }
    body {
      min-height: 100vh;
      width: 100vw;
      background: var(--main-bg);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', Arial, sans-serif;
      margin: 0;
    }
    .form-container {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 4px 24px rgba(172,22,0,0.07);
      padding: 2.7rem 2.2rem 2rem 2.2rem;
      min-width: 340px;
      max-width: 370px;
    }
    h2 {
      text-align: center;
      margin-top: 0;
      color: var(--special-color);
      letter-spacing: 1px;
      margin-bottom: 1.7rem;
    }
    form {
      display: flex;
      flex-direction: column;
    }
    input[type="email"],
    input[type="password"] {
      font-size: 1.1rem;
      padding: 0.85rem 0.9rem;
      border: 1.4px solid var(--input-border);
      border-radius: 7px;
      margin-bottom: 1.2rem;
      transition: border .2s;
      background: #fcf7f0;
      color: #222;
    }
    input[type="email"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: var(--special-color);
    }
    button[type="submit"] {
      padding: 1rem 0;
      background: var(--btn-bg);
      color: var(--btn-color);
      font-size: 1.09rem;
      font-weight: 600;
      border: none;
      border-radius: 7px;
      cursor: pointer;
      margin-top: .5rem;
      transition: background .18s;
      box-shadow: 0 2px 7px rgba(172,22,0,0.07);
    }
    button[type="submit"]:hover {
      background: #8e1200;
    }
    .error-message {
      background: var(--error-color);
      color: #fff;
      font-size: 1rem;
      border-radius: 7px;
      padding: 0.9em 1.2em;
      margin-bottom: 1.2rem;
      text-align: center;
      letter-spacing: .3px;
      box-shadow: 0 2px 8px rgba(171,60,20,0.09);
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Admin Login</h2>
    <?php if ($message): ?>
      <div class="error-message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
      <input type="email" name="email" required placeholder="Admin Email">
      <input type="password" name="password" required placeholder="Password">
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>