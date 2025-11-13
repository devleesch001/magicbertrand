<?php
declare(strict_types=1);

require 'autoloader.php';


use lib\Utils;
use lib\Databases;

// Incrémente le compteur à chaque hit (GET/POST)
$count = 0;
$error = null;
try {
//    $count = Databases::increment_visit_count();
    Databases::increment_unique_visit_count(Utils::hkey());
    $count = Databases::get_unique_visit_count();
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
  <div class="badge badge-animated error" role="alert">
    Erreur compteur
    <span class="small"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
  </div>
<?php else: ?>
  <div class="badge badge-animated" aria-live="polite" aria-atomic="true">
    Visites: <b><?php echo htmlspecialchars((string)$count, ENT_QUOTES, 'UTF-8'); ?></b>
  </div>
<?php endif; ?>

<a class="badge-animated gh-btn" href="https://github.com/devleesch001/magicbertrand" target="_blank" rel="noopener noreferrer" aria-label="Ouvrir GitHub dans un nouvel onglet">★ GitHub ★</a>

<main>
    <section class="hero" aria-label="Titre principal">
        <h1>MAGIC <br>BERTRAND</h1>
    </section>

    <section class="marquees" aria-hidden="true">
        <div class="marquee left" style="--duration: 20s">
            <div class="track">
                <span>MagicBertrand ✦ MagicBertrand ✦ MagicBertrand ✦ MagicBertrand ✦</span>
                <span aria-hidden="true">MagicBertrand ✦ MagicBertrand ✦ MagicBertrand ✦ MagicBertrand ✦</span>
            </div>
        </div>
        <div class="marquee right" style="--duration: 12s">
            <div class="track">
                <span>MAGICBERTRAND MAGICBERTRAND MAGICBERTRAND MAGICBERTRAND MAGICBERTRAND</span>
                <span aria-hidden="true">MAGICBERTRAND MAGICBERTRAND MAGICBERTRAND MAGICBERTRAND MAGICBERTRAND</span>
            </div>
        </div>
        <div class="marquee right" style="--duration: 25s">
            <div class="track">
                <span>★ MAGIC • BERTRAND • MAGIC • BERTRAND • MAGIC • BERTRAND ★</span>
                <span aria-hidden="true">★ MAGIC • BERTRAND • MAGIC • BERTRAND • MAGIC • BERTRAND ★</span>
            </div>
        </div>
        <div class="marquee left" style="--duration: 16s">
            <div class="track">
                <span>M A G I C B E R T R A N D — M A G I C B E R T R A N D — M A G I C</span>
                <span aria-hidden="true">M A G I C B E R T R A N D — M A G I C B E R T R A N D — M A G I C</span>
            </div>
        </div>
    </section>

    <div id="chaos" aria-hidden="true"></div>

    <div class="sticker">
        <iframe src="https://www.youtube.com/embed/_G6Gj7PEGS8?si=2wX1-ORFCnDTVXF5&start=5326" title="Magic Bertrand Mega BG" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    </div>
</main>
</body>
</html>
