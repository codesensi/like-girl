<?php
require_once dirname(__DIR__, 2) . '/app/Admin/Controllers/AuthController.php';
(new AuthController())->loginPost();
