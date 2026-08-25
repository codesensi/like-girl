<?php
require_once dirname(__DIR__, 2) . '/app/Admin/Controllers/SettingController.php';
(new SettingController())->updateCouple();
