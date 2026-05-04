<?php
require_once 'db.php';
require_once 'helpers.php';

$featuredSql = "SELECT id, title, description, image, category, location, event_date FROM events ORDER BY event_date DESC LIMIT 3";
$featuredResult = $conn->query($featuredSql);

$latestSql = "SELECT id, title, description, image, category, location, event_date FROM events ORDER BY id DESC LIMIT 8";
$latestResult = $conn->query($latestSql);

$categoriesSql = "SELECT category, COUNT(*) AS total FROM events WHERE category IS NOT NULL AND category <> '' GROUP BY category ORDER BY total DESC LIMIT 6";
$categoriesResult = $conn->query($categoriesSql);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دليل فعاليات الجامعة الافتراضية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<?php include 'partials/header.php'; ?>

<main class="container my-4">
    <section class="hero-panel p-4 p-md-5 mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-6 mb-3">منصة موحدة لفعاليات الجامعة الافتراضية السورية</h1>
                <p class="mb-0">تابع أحدث الأنشطة الأكاديمية والثقافية والرياضية والعائلية، مع صفحة تفاصيل لكل فعالية وإدارة كاملة من لوحة التحكم.</p>
            </div>
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="section-card p-3 text-dark">
                    <h5>بيانات فريق المشروع</h5>
                    <p class="mb-1"><strong>المادة:</strong> BWP501</p>
                    <p class="mb-1"><strong>إشراف:</strong> د. باسل الخطيب</p>
                    <p class="mb-0"><strong>المجموعة:</strong> 2026-2-05-10</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="section-title mb-0">فعاليات بارزة هذا الأسبوع</h2>
            <a class="btn btn-outline-primary" href="events.php">كل الفعاليات</a>
        </div>

        <div id="featuredEventsCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner rounded-4">
                <?php if ($featuredResult && $featuredResult->num_rows > 0): ?>
                    <?php $isActive = true; while ($row = $featuredResult->fetch_assoc()): ?>
                        <?php $featuredImage = event_image_url($row['image'], $row['category'], (string)$row['id']); ?>
                        <?php $featuredFallback = fallback_image_url((string)$row['id']); ?>
                        <div class="carousel-item <?php echo $isActive ? 'active' : ''; ?>">
                            <img src="<?php echo htmlspecialchars($featuredImage); ?>" onerror="this.onerror=null;this.src='<?php echo htmlspecialchars($featuredFallback, ENT_QUOTES); ?>';" class="d-block w-100" alt="<?php echo htmlspecialchars($row['title']); ?>">
                            <div class="carousel-caption text-start bg-dark bg-opacity-50 rounded-3 p-3">
                                <h5><?php echo htmlspecialchars($row['title']); ?></h5>
                                <p><?php echo htmlspecialchars(mb_substr($row['description'], 0, 130)) . '...'; ?></p>
                                <a class="btn btn-sm btn-warning" href="event.php?id=<?php echo (int)$row['id']; ?>">عرض التفاصيل</a>
                            </div>
                        </div>
                    <?php $isActive = false; endwhile; ?>
                <?php else: ?>
                    <div class="carousel-item active">
                        <img src="assets/img/event1.jpg" class="d-block w-100" alt="لا توجد فعاليات">
                        <div class="carousel-caption bg-dark bg-opacity-50 rounded-3 p-3">
                            <h5>لا توجد فعاليات مضافة حاليًا</h5>
                            <p>ابدأ بإضافة الفعاليات من لوحة التحكم.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#featuredEventsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#featuredEventsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </section>

    <section class="section-card p-4 mb-4">
        <h2 class="section-title">تصنيفات سريعة</h2>
        <?php if ($categoriesResult && $categoriesResult->num_rows > 0): ?>
            <?php while ($category = $categoriesResult->fetch_assoc()): ?>
                <a class="category-chip" href="events.php?category=<?php echo urlencode($category['category']); ?>">
                    <i class="fa-solid fa-tag"></i>
                    <?php echo htmlspecialchars($category['category']); ?>
                    <span class="badge text-bg-light"><?php echo (int)$category['total']; ?></span>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-muted mb-0">لا توجد تصنيفات بعد.</p>
        <?php endif; ?>
    </section>

    <section class="section-card p-4">
        <h2 class="section-title">أحدث الفعاليات</h2>
        <div class="row g-4">
            <?php if ($latestResult && $latestResult->num_rows > 0): ?>
                <?php while ($event = $latestResult->fetch_assoc()): ?>
                    <?php $latestImage = event_image_url($event['image'], $event['category'], (string)$event['id']); ?>
                    <?php $latestFallback = fallback_image_url((string)$event['id']); ?>
                    <div class="col-md-6 col-lg-3">
                        <article class="card event-card h-100">
                            <img src="<?php echo htmlspecialchars($latestImage); ?>" onerror="this.onerror=null;this.src='<?php echo htmlspecialchars($latestFallback, ENT_QUOTES); ?>';" class="card-img-top" alt="<?php echo htmlspecialchars($event['title']); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                                <p class="event-meta mb-2"><i class="fa-regular fa-calendar"></i> <?php echo date('Y-m-d', strtotime($event['event_date'])); ?></p>
                                <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars(mb_substr($event['description'], 0, 80)) . '...'; ?></p>
                                <a href="event.php?id=<?php echo (int)$event['id']; ?>" class="btn btn-primary btn-sm mt-2">التفاصيل</a>
                            </div>
                        </article>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted">لم يتم إضافة فعاليات بعد.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="section-card p-4 mt-4">
        <h2 class="section-title">المشاركون في المشروع</h2>
        <div class="row g-3">
            <div class="col-md-6 col-lg-4"><div class="p-3 border rounded-3">أحمد رحمون | ahmad_187832 c1</div></div>
            <div class="col-md-6 col-lg-4"><div class="p-3 border rounded-3">ملدا قدور | mulda_197917_C3</div></div>
            <div class="col-md-6 col-lg-4"><div class="p-3 border rounded-3">عبدالرحمن داغستاني | abdalrhman_176558 C1</div></div>
            <div class="col-md-6 col-lg-4"><div class="p-3 border rounded-3">فداء فوزي | fedaa_290944</div></div>
            <div class="col-md-6 col-lg-4"><div class="p-3 border rounded-3">بيسان احمد | bisan_266673</div></div>
        </div>
    </section>
</main>

<?php include 'partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
