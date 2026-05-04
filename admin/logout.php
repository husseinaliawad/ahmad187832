<?php
// 1. بدء الجلسة
session_start();

// 2. إلغاء تعيين جميع متغيرات الجلسة
$_SESSION = array();

// 3. تدمير الجلسة
session_destroy();

// 4. توجيه المستخدم إلى صفحة تسجيل الدخول
header("location: login.php");
exit;
?>
