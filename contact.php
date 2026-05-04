<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اتصل بنا - دليل فعاليات الجامعة الافتراضية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<?php include 'partials/header.php'; ?>

<main class="container my-4">
    <section class="section-card p-4 p-md-5">
        <h1 class="section-title">اتصل بنا</h1>
        <p class="text-muted">املأ النموذج التالي وسيظهر لك إشعار نجاح أو خطأ عبر Bootstrap Alerts.</p>

        <div id="contactFeedback" class="alert d-none"></div>

        <form id="contactForm" novalidate>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">الاسم الكامل</label>
                    <input type="text" class="form-control" id="name" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" class="form-control" id="email" required>
                </div>
                <div class="col-12">
                    <label for="message" class="form-label">الرسالة</label>
                    <textarea class="form-control" id="message" rows="6" required></textarea>
                </div>
                <div class="col-12 d-grid d-md-block">
                    <button type="submit" class="btn btn-primary">إرسال</button>
                </div>
            </div>
        </form>

        <hr class="my-4">
        <div class="row g-3">
            <div class="col-md-6">
                <h2 class="h5">معلومات تواصل بديلة</h2>
                <p class="mb-1">البريد العام: <a href="mailto:info@cityevents.com">info@cityevents.com</a></p>
                <p class="mb-0">البريد الأكاديمي: <a href="mailto:t_balkhatib@svuonline.org">t_balkhatib@svuonline.org</a></p>
            </div>
            <div class="col-md-6">
                <h2 class="h5">حسابات التواصل</h2>
                <a href="#" class="btn btn-outline-primary btn-sm me-2">Facebook</a>
                <a href="#" class="btn btn-outline-danger btn-sm">Instagram</a>
            </div>
        </div>
    </section>
</main>

<?php include 'partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
