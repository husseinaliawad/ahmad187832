<?php
require_once 'db.php';
require_once 'helpers.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('رابط فعالية غير صالح.');
}

$eventId = (int)$_GET['id'];
$stmt = $conn->prepare('SELECT * FROM events WHERE id = ?');
$stmt->bind_param('i', $eventId);
$stmt->execute();
$eventResult = $stmt->get_result();

if ($eventResult->num_rows === 0) {
    die('الفعالية غير موجودة.');
}

$event = $eventResult->fetch_assoc();

$relatedStmt = $conn->prepare('SELECT id, title, event_date, image FROM events WHERE category = ? AND id <> ? ORDER BY event_date DESC LIMIT 3');
$relatedStmt->bind_param('si', $event['category'], $eventId);
$relatedStmt->execute();
$relatedEvents = $relatedStmt->get_result();

$startDateGcal = date('Ymd\THis', strtotime($event['event_date']));
$endDateGcal = date('Ymd\THis', strtotime($event['event_date'] . ' +2 hours'));
$googleCalendarLink = 'https://www.google.com/calendar/render?action=TEMPLATE';
$googleCalendarLink .= '&text=' . urlencode($event['title']);
$googleCalendarLink .= '&dates=' . $startDateGcal . '/' . $endDateGcal;
$googleCalendarLink .= '&details=' . urlencode($event['description']);
$googleCalendarLink .= '&location=' . urlencode($event['location']);

$mainEventImage = event_image_url($event['image'], $event['category'], (string)$event['id']);
$mainEventFallback = fallback_image_url((string)$event['id']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title']); ?> - دليل الفعاليات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<?php include 'partials/header.php'; ?>

<main class="container my-4">
    <section class="section-card p-4 mb-4">
        <div class="row g-4">
            <div class="col-lg-6">
                <img src="<?php echo htmlspecialchars($mainEventImage); ?>" onerror="this.onerror=null;this.src='<?php echo htmlspecialchars($mainEventFallback, ENT_QUOTES); ?>';" class="img-fluid rounded-4 w-100" alt="<?php echo htmlspecialchars($event['title']); ?>">
            </div>
            <div class="col-lg-6">
                <h1 class="mb-3"><?php echo htmlspecialchars($event['title']); ?></h1>
                <p class="event-meta mb-3">
                    <i class="fa-regular fa-calendar"></i> <?php echo date('Y-m-d H:i', strtotime($event['event_date'])); ?>
                    <br>
                    <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($event['location']); ?>
                    <br>
                    <i class="fa-solid fa-tag"></i> <?php echo htmlspecialchars($event['category']); ?>
                </p>
                <p style="white-space: pre-line;"><?php echo htmlspecialchars($event['description']); ?></p>

                <div class="d-flex flex-wrap gap-2 mt-3">
                    <a href="<?php echo $googleCalendarLink; ?>" class="btn btn-success" target="_blank" rel="noopener">أضف للتقويم</a>
                    <button id="shareEventBtn" type="button" class="btn btn-info text-white" data-title="<?php echo htmlspecialchars($event['title']); ?>" data-url="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">شارك</button>
                </div>
            </div>
        </div>
    </section>

    <section class="section-card p-4">
        <h2 class="h4 mb-3">فعاليات ذات صلة</h2>
        <div class="row g-4">
            <?php if ($relatedEvents && $relatedEvents->num_rows > 0): ?>
                <?php while ($related = $relatedEvents->fetch_assoc()): ?>
                    <?php $relatedImage = event_image_url($related['image'], $event['category'], (string)$related['id']); ?>
                    <?php $relatedFallback = fallback_image_url((string)$related['id']); ?>
                    <div class="col-md-4">
                        <article class="card h-100 event-card">
                            <img src="<?php echo htmlspecialchars($relatedImage); ?>" onerror="this.onerror=null;this.src='<?php echo htmlspecialchars($relatedFallback, ENT_QUOTES); ?>';" class="card-img-top" alt="<?php echo htmlspecialchars($related['title']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($related['title']); ?></h5>
                                <p class="event-meta"><?php echo date('Y-m-d', strtotime($related['event_date'])); ?></p>
                                <a href="event.php?id=<?php echo (int)$related['id']; ?>" class="btn btn-outline-primary btn-sm">عرض الفعالية</a>
                            </div>
                        </article>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted mb-0">لا توجد فعاليات أخرى ضمن نفس التصنيف.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
