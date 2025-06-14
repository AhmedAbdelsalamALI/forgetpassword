<?php
header("Content-Type: application/json");

// أمثلة على محتوى عام (تقدر تغيره لاحقًا)
echo json_encode([
    ["title" => "مقال عام 1", "body" => "محتوى المقال الأول"],
    ["title" => "مقال عام 2", "body" => "محتوى المقال الثاني"]
]);
?>
