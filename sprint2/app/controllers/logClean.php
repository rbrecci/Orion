<?php

include (__DIR__ . "/../middleware/admin.php");

$sql = "DELETE FROM activity_logs";
$conn->query($sql);

header("Location: ../views/admin/");
exit;

?>