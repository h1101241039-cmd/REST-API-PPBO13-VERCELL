<?php

// API ENTRY POINT
// Jangan pakai index.php di root untuk routing API
// Panggil langsung controller dan middleware

require_once __DIR__ . '/../core/AuthMiddleware.php';
require_once __DIR__ . '/../api/MahasiswaController.php';

// Route dan logic ada di bawah:
require __DIR__ . '/../router.php';
