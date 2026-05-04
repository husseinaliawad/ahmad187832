<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: dashboard.php');
    exit;
}

require_once '../db.php';

$username = '';
$loginErr = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $loginErr = 'يرجى إدخال اسم المستخدم وكلمة المرور.';
    } elseif ($username === 'admin' && $password === 'admin') {
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = 0;
        $_SESSION['username'] = 'admin';
        header('Location: dashboard.php');
        exit;
    } else {
        $stmt = $conn->prepare('SELECT id, username, password FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if ($password === $user['password']) {
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = (int)$user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: dashboard.php');
                exit;
            }
        }

        $loginErr = 'بيانات الدخول غير صحيحة.';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
    <div class="container login-wrapper">
        <div class="admin-card p-4 p-md-5">
            <h1 class="h3 text-center mb-3">لوحة التحكم</h1>
            <p class="text-center text-muted mb-4">الدخول الافتراضي للتصحيح: <strong>admin / admin</strong></p>

            <?php if ($loginErr): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($loginErr); ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">اسم المستخدم</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">كلمة المرور</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">تسجيل الدخول</button>
                </div>
            </form>
            <a href="../index.php" class="btn btn-link mt-3 px-0">العودة للموقع</a>
        </div>
    </div>
</body>
</html>
