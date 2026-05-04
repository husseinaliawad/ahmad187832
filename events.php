<?php
require_once 'db.php';
require_once 'helpers.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$date = isset($_GET['date']) ? trim($_GET['date']) : '';

$where = [];
$params = [];
$types = '';

if ($search !== '') {
    $where[] = "(title LIKE ? OR description LIKE ? OR location LIKE ?)";
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
}

if ($category !== '') {
    $where[] = "category = ?";
    $params[] = $category;
    $types .= 's';
}

if ($date !== '') {
    $where[] = "DATE(event_date) = ?";
    $params[] = $date;
    $types .= 's';
}

$sql = "SELECT id, title, description, category, location, event_date, image FROM events";
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY event_date DESC';

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$categoriesResult = $conn->query("SELECT DISTINCT category FROM events WHERE category IS NOT NULL AND category <> '' ORDER BY category ASC");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الفعاليات - دليل فعاليات الجامعة الافتراضية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<?php include 'partials/header.php'; ?>

<main class="container my-4">
    <section class="filter-panel p-4 mb-4">
        <h1 class="section-title">كل الفعاليات</h1>
        <form method="GET" action="events.php" class="row g-3">
            <div class="col-lg-4">
                <label class="form-label">بحث</label>
                <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="العنوان أو الوصف أو المكان">
            </div>
            <div class="col-lg-3">
                <label class="form-label">التصنيف</label>
                <select class="form-select" name="category">
                    <option value="">كل التصنيفات</option>
                    <?php if ($categoriesResult): while ($cat = $categoriesResult->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category']); ?>
                        </option>
                    <?php endwhile; endif; ?>
                </select>
            </div>
            <div class="col-lg-3">
                <label class="form-label">التاريخ</label>
                <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($date); ?>">
            </div>
            <div class="col-lg-2 d-grid align-content-end">
                <button class="btn btn-primary mt-2" type="submit">فلترة</button>
            </div>
        </form>
    </section>

    <section class="section-card p-4">
        <div class="d-flex justify-content-between mb-3">
            <h2 class="h4">نتائج البحث</h2>
            <span class="badge text-bg-primary p-2"><?php echo $result ? $result->num_rows : 0; ?> فعالية</span>
        </div>

        <div class="row g-4">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($event = $result->fetch_assoc()): ?>
                    <?php $eventImage = event_image_url($event['image'], $event['category'], (string)$event['id']); ?>
                    <?php $eventFallback = fallback_image_url((string)$event['id']); ?>
                    <div class="col-md-6 col-lg-4">
                        <article class="card event-card h-100">
                            <img src="<?php echo htmlspecialchars($eventImage); ?>" onerror="this.onerror=null;this.src='<?php echo htmlspecialchars($eventFallback, ENT_QUOTES); ?>';" class="card-img-top" alt="<?php echo htmlspecialchars($event['title']); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                                <p class="event-meta mb-2">
                                    <i class="fa-regular fa-calendar"></i> <?php echo date('Y-m-d H:i', strtotime($event['event_date'])); ?>
                                    <br>
                                    <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($event['location']); ?>
                                    <br>
                                    <i class="fa-solid fa-tag"></i> <?php echo htmlspecialchars($event['category']); ?>
                                </p>
                                <p class="text-muted flex-grow-1"><?php echo htmlspecialchars(mb_substr($event['description'], 0, 120)) . '...'; ?></p>
                                <a href="event.php?id=<?php echo (int)$event['id']; ?>" class="btn btn-outline-primary mt-auto">التفاصيل</a>
                            </div>
                        </article>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted mb-0">لا توجد نتائج مطابقة. جرّب تغيير معايير الفلترة.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
