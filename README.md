# MagicBertrand (PHP 8.4 + SQLite)

Site Non Officiel du magicien « Magic Bertrand ».

De la vidéo [LES 624 TYPES DE PROFS ! (Feat. Monsieur Frisé) - CHAISE](https://www.youtube.com/watch?v=_G6Gj7PEGS8&t=5326)
 
## Aperçu des fonctionnalités
- Fond arc‑en‑ciel animé avec lueurs et saturations agressives.
- Gros titre « MAGIC BERTRAND » qui clignote et pulse.
- Lignes de texte défilantes façon « marquee » (implémentées en CSS, sans balise `<marquee>`).
- Générateur JS qui place aléatoirement des dizaines d’éléments « MagicBertrand » animés (rotation, pulsation, clignotement, variations de teinte).
- Sticker vidéo YouTube qui « wiggle » dans un coin.
- Compteur de visites persistant en base SQLite, incrémenté à chaque hit.

## Prérequis
- PHP 8.4 (ou 8.x) avec l’extension `SQLite3` activée.
- Accès en écriture au dossier `data/` à la racine du projet (créé automatiquement). À défaut, un fallback utilise le dossier temporaire du système (`/tmp`).

## Structure du projet
```
.
├── index.php        # Page principale qui incrémente et affiche le compteur
├── sql.php          # Module SQLite: ouverture DB, création table, incrément
├── styles.css       # Fond arc-en-ciel, titres, stickers, marquees CSS
├── script.js        # Générateur d’éléments « MagicBertrand » animés
└── data/            # (créé à la volée) contiendra db.sqlite3
```

## Détails techniques (compteur SQLite)
- Fichier DB : `data/db.sqlite3` (ou fallback `/tmp/magicbertrand_visits.sqlite3`).
- Table `counter` avec une seule ligne (`id=1`, `count INTEGER`).
- Incrément atomique : transaction `BEGIN IMMEDIATE` pour éviter les races.
- PRAGMA activés : `journal_mode=WAL`, `synchronous=NORMAL`, `foreign_keys=ON`, `busyTimeout(3000)`.

Points d’entrée PHP :
- `increment_visit_count(): int` – incrémente et retourne la valeur courante.
- `get_visit_count(): int` – lit la valeur sans incrémenter.

## Licence
CC Zero (CC0) Usage à votre convenance.

## Remerciements
- À [CHAISE](https://www.youtube.com/@CHAISEORG) pour son cerveau que l'IA ne remplacera jamais.
- À [Monsieur Frisé](https://www.youtube.com/c/Timoth%C3%A9eHochet), pour ses talents magiques inégalés.
