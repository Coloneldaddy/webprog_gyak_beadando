<?php
session_start();

$config = require 'config.php';
require_once 'db/db.php';

$page = $_GET['page'] ?? 'home';

// Engedélyezett oldalak bővítve
$allowedPages = [
    'home',
    'cars',       // autók lista + feltöltés
    'car',        // részletes autóoldal
    'edit_car',   // szerkesztés
    'delete_car', // törlés
    'contact',
    'messages',
    'login',
    'logout'
];
if (!in_array($page, $allowedPages, true)) {
    $page = 'home';
}

function getMenu(array $config): array {
    $menu = $config['menu'];
    if (isset($_SESSION['user']['login'])) {
        unset($menu['login']);
    } else {
        unset($menu['logout'], $menu['messages']);
    }
    return $menu;
}

include 'views/header.php';

$menu = getMenu($config);
include 'views/menu.php';

include "controllers/{$page}.php";

include 'views/footer.php';
