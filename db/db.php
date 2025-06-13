<?php
// Itt állítsd be az adatbázis elérését!
$dbh = new PDO(
    'mysql:host=localhost;dbname=nuk911',
    'nuk911',
    'nuk911',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
