<?php
require_once dirname(__DIR__, 2) . '/app/Admin/Controllers/LeavingController.php';
(new LeavingController())->destroy();
