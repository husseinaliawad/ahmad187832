<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';

$status = $_GET['status'] ?? '';
$eventsResult = $conn->query('SELECT id, title, category, location, event_date FROM events ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - إدارة الفعاليات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
<nav class="navbar navbar-expand-lg admin-navbar navbar-dark">
    <div class="container admin-shell">
        <a class="navbar-brand" href="dashboard.php">لوحة التحكم</a>
        <div class="ms-auto d-flex gap-2 align-items-center">
            <span class="text-white">مرحبًا <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="btn btn-sm btn-outline-light">خروج</a>
        </div>
    </div>
</nav>

<main class="container admin-shell py-4">
    <div class="admin-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">إدارة الفعاليات</h1>
            <a href="add_event.php" class="btn btn-success">إضافة فعالية</a>
        </div>

        <?php if ($status === 'added'): ?>
            <div class="alert alert-success">تمت إضافة الفعالية بنجاح.</div>
        <?php elseif ($status === 'updated'): ?>
            <div class="alert alert-primary">تم تحديث الفعالية بنجاح.</div>
        <?php elseif ($status === 'deleted'): ?>
            <div class="alert alert-warning">تم حذف الفعالية.</div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>العنوان</th>
                        <th>التصنيف</th>
                        <th>المكان</th>
                        <th>التاريخ</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($eventsResult && $eventsResult->num_rows > 0): ?>
                        <?php while ($row = $eventsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo (int)$row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($row['event_date'])); ?></td>
                                <td>
                                    <a href="edit_event.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-primary">تعديل</a>
                                    <a href="delete_event.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('تأكيد حذف الفعالية؟');">حذف</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">لا توجد فعاليات حتى الآن.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>
