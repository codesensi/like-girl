<?php
require_once dirname(__DIR__, 2) . '/app/Admin/Controllers/UserController.php';
(new UserController())->index();
