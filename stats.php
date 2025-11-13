<?php
declare(strict_types=1);
require 'autoloader.php';

use lib\Databases;

$unique_visit = 0;
$total_visit = 0;
$error = null;
try {
    $unique_visit = Databases::get_unique_visit_count();
    $total_visit = Databases::get_unique_visit_sum();

} catch (Throwable $e) {
    // Si SQLite n'est pas dispo, on évite de casser l'affichage
    $count = -1; // indicateur d'erreur
    $error = $e->getMessage();
}

?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Magic Bertrand - Méga Kitsch</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php if ($error): ?>
  <div role="alert">
    Erreur DB : <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
  </div>
<?php else: ?>
  <div aria-live="polite" aria-atomic="true">
    <table>
        <thead>
        <tr>
            <td>Data</td>
            <td>Value</td>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>Unique visit</td>
                <td><?php echo $unique_visit ?></td>
            </tr>
            <tr>
                <td>Total visit</td>
                <td><?php echo $total_visit ?></td>
            </tr>
        </tbody>
    </table>
  </div>
<?php endif; ?>
<main>

</main>
</body>
</html>
