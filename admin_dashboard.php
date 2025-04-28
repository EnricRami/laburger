<?php
session_start();
require 'dp.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$orders = $conn->query(
    "SELECT o.id, o.user_id, o.total, o.order_date, o.status,
            u.username, u.email AS user_email
     FROM orders o
     JOIN users u ON o.user_id = u.id
     ORDER BY o.order_date DESC"
);

$messages = $conn->query(
    "SELECT id, name, email, message, submitted_at
     FROM contact_us
     ORDER BY submitted_at DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - La Burger</title>
    <style>
      :root {
        --special-color: #AC1600;
        --main-bg: #FCFCE4;
        --table-head: #f6ab83;
      }
      body {
        background: var(--main-bg);
        min-height: 100vh;
        font-family: 'Poppins', Arial, sans-serif;
        margin: 0;
        padding: 0;
        color: #222;
      }
      .dashboard-container {
        max-width: 1100px;
        margin: 3rem auto;
        background: #fff;
        border-radius: 20px;
        padding: 2rem 2.5rem 2.5rem 2.5rem;
        box-shadow: 0 6px 32px rgba(172,22,0,0.05);
      }
      h1 {
        color: var(--special-color);
        margin-top: 0;
        margin-bottom: .5em;
        font-size: 2.1em;
        letter-spacing: 0.5px;
        text-align: center;
      }
      h2 {
        color: var(--special-color);
        margin-top: 2.2em;
        margin-bottom: 0.5em;
      }
      table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 35px;
        background: #fafafa;
        border-radius: 9px;
        overflow: hidden; /* for radius */
        font-size: 1.03em;
        box-shadow: 0 2px 8px rgba(180,90,63,0.05);
      }
      th, td {
        padding: 13px 11px;
        text-align: left;
      }
      th {
        background: var(--table-head);
        color: #fff;
        font-weight: 600;
        letter-spacing: .5px;
        font-size: 1.01em;
      }
      tr:nth-child(even) td {
        background: #f7ebdc;
      }
      tr:hover td {
        background: #ffecdb9b;
      }
      .logout {
        float: right;
        background: var(--special-color);
        color: #fff;
        padding: 0.6em 1em;
        border-radius: 7px;
        box-shadow: 0 2px 6px rgba(172,22,0,0.06);
        font-weight: 500;
        text-decoration: none;
        font-size: 1em;
        margin-top: -10px;
        margin-bottom: 16px;
        transition: background 0.15s;
      }
      .logout:hover {
        background: #852000;
      }
      @media (max-width: 800px) {
        .dashboard-container {
          padding: 1rem 0.4rem;
          font-size: .97em;
        }
        table, th, td {
          font-size: .95em;
        }
      }
    </style>
</head>
<body>
  <div class="dashboard-container">
    <a href="admin_logout.php" class="logout">Logout</a>
    <h1>Admin Dashboard</h1>

    <h2>Orders</h2>
    <table>
        <tr>
            <th>Order ID</th><th>User</th><th>Email</th>
            <th>Total</th><th>Date</th><th>Status</th>
        </tr>
        <?php while ($o = $orders->fetch_assoc()): ?>
        <tr>
            <td><?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['username']) ?></td>
            <td><?= htmlspecialchars($o['user_email']) ?></td>
            <td>$<?= number_format($o['total'], 2) ?></td>
            <td><?= $o['order_date'] ?></td>
            <td><?= htmlspecialchars($o['status']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Contact Messages</h2>
    <table>
        <tr>
            <th>ID</th><th>Name</th><th>Email</th>
            <th>Message</th><th>Submitted At</th>
        </tr>
        <?php while ($m = $messages->fetch_assoc()): ?>
        <tr>
            <td><?= $m['id'] ?></td>
            <td><?= htmlspecialchars($m['name']) ?></td>
            <td><?= htmlspecialchars($m['email']) ?></td>
            <td><?= nl2br(htmlspecialchars($m['message'])) ?></td>
            <td><?= $m['submitted_at'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
  </div>
</body>
</html>