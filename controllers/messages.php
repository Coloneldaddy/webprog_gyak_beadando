<?php
// controllers/messages.php
if (!isset($_SESSION['user'])) {
    echo '<div class="alert alert-warning">Az üzenetek megtekintéséhez jelentkezz be!</div>';
    return;
}
require_once(__DIR__ . '/../db/db.php');
$stmt = $dbh->query("SELECT * FROM messages ORDER BY created_at DESC");
?>

<h2>Beérkezett üzenetek</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Név</th>
            <th>E-mail</th>
            <th>Üzenet</th>
            <th>Időpont</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($stmt as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
