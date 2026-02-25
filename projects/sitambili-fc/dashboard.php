<?php
// dashboard.php - View form submissions for Sitambili FC
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sitambili_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, name, email, subject, message, created_at FROM messages ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sita Mbili FC | Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0033A0;
            --secondary: #FFD700;
            --bg: #F4F4F9;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg); padding: 2rem; color: #333; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .header h1 { color: var(--primary); font-weight: 800; margin: 0; }
        .card { background: #fff; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th { text-align: left; padding: 1rem; background: var(--primary); color: #fff; }
        th:first-child { border-top-left-radius: 8px; }
        th:last-child { border-top-right-radius: 8px; }
        td { padding: 1rem; border-bottom: 1px solid #eee; }
        tr:hover { background: #f9f9f9; }
        .badge { background: #eee; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem; }
        .empty { text-align: center; padding: 3rem; color: #888; }
        .btn-refresh { background: var(--primary); color: #fff; padding: 0.6rem 1.2rem; border-radius: 6px; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-database"></i> Contact Database</h1>
            <a href="dashboard.php" class="btn-refresh"><i class="fas fa-sync"></i> Refresh</a>
        </div>

        <div class="card">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact Info</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                                <td>
                                    <div class="badge"><?php echo htmlspecialchars($row['email']); ?></div>
                                    <div style="font-size: 0.75rem; margin-top: 5px; color: #666;">
                                        Subject: <?php echo htmlspecialchars($row['subject']); ?>
                                    </div>
                                </td>
                                <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                                <td style="font-size: 0.85rem; color: #888;"><?php echo $row['created_at']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">
                    <i class="fas fa-inbox fa-3x" style="margin-bottom: 1rem; opacity: 0.3;"></i>
                    <p>No messages yet. Try submitting the contact form!</p>
                </div>
            <?php endif; ?>
        </div>
        <p style="margin-top: 2rem; font-size: 0.8rem; text-align: center; color: #888;">
            Sitambili FC Admin Panel &bull; Powered by XAMPP
        </p>
    </div>
</body>
</html>
<?php $conn->close(); ?>
