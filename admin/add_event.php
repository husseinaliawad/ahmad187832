<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../db.php';

$title = '';
$description = '';
$category = '';
$location = '';
$eventDate = '';
$imageErr = '';
$formErr = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');

    if ($title === '' || $description === '' || $eventDate === '') {
        $formErr = 'الحقول الإلزامية: العنوان، الوصف، التاريخ.';
    }

    $imagePathForDb = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = '../assets/img/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['image']['name']);
        $targetFile = $targetDir . $imageName;
        $imageType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($imageType, $allowed, true)) {
            $imageErr = 'صيغة الصورة غير مدعومة.';
        } elseif (getimagesize($_FILES['image']['tmp_name']) === false) {
            $imageErr = 'الملف المرفوع ليس صورة.';
        } elseif (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imageErr = 'فشل رفع الصورة.';
        } else {
            $imagePathForDb = 'assets/img/' . $imageName;
        }
    } else {
        $imageErr = 'الصورة مطلوبة.';
    }

    if ($formErr === '' && $imageErr === '') {
        $stmt = $conn->prepare('INSERT INTO events (title, description, category, location, event_date, image) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssss', $title, $description, $category, $location, $eventDate, $imagePathForDb);
        if ($stmt->execute()) {
            header('Location: dashboard.php?status=added');
            exit;
        }
        $formErr = 'حدث خطأ أثناء إضافة الفعالية.';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة فعالية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
<nav class="navbar navbar-dark admin-navbar">
    <div class="container admin-shell"><a class="navbar-brand" href="dashboard.php">لوحة التحكم</a></div>
</nav>

<main class="container admin-shell py-4">
    <div class="admin-card p-4">
        <h1 class="h4 mb-3">إضافة فعالية جديدة</h1>

        <?php if ($formErr): ?><div class="alert alert-danger"><?php echo htmlspecialchars($formErr); ?></div><?php endif; ?>
        <?php if ($imageErr): ?><div class="alert alert-warning"><?php echo htmlspecialchars($imageErr); ?></div><?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">العنوان *</label>
                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">التصنيف</label>
                <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($category); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">المكان</label>
                <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($location); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">التاريخ والوقت *</label>
                <input type="datetime-local" name="event_date" class="form-control" value="<?php echo htmlspecialchars($eventDate); ?>">
            </div>
            <div class="col-12">
                <label class="form-label">الوصف *</label>
                <textarea name="description" class="form-control" rows="5"><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">صورة الفعالية *</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="col-12">
                <button class="btn btn-primary" type="submit">حفظ</button>
                <a href="dashboard.php" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</main>
</body>
</html>
