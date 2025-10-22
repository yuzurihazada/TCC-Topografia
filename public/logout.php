<?php
require __DIR__ . '/admin/config.php';

logout_admin();

header('Location: ./login.php');
exit;
