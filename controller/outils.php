<?php
// path: controller/outils.php
// Petites fonctions utilisées par les contrôleurs et les vues.

// champ caché du jeton anti-CSRF : à mettre dans chaque formulaire POST
function champCsrf(): string
{
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '" />';
}

// le jeton reçu est-il celui de la session ? (sinon la requête vient peut-être d'un autre site)
function verifierCsrf(): bool
{
    $jeton = $_POST['csrf_token'] ?? '';
    return is_string($jeton) && hash_equals($_SESSION['csrf_token'], $jeton);
}

// lit un champ texte du formulaire, sans espaces autour ('' s'il est absent)
function lireTexte(string $cle): string
{
    $valeur = $_POST[$cle] ?? '';
    return is_string($valeur) ? trim($valeur) : '';
}

// lit un nombre entier du formulaire entre $min et $max (null s'il est absent, mal écrit ou hors limites)
function lireEntier(string $cle, int $min, int $max): ?int
{
    $valeur = filter_var(lireTexte($cle), FILTER_VALIDATE_INT);
    return ($valeur === false || $valeur < $min || $valeur > $max) ? null : $valeur;
}

// ce que la personne avait tapé dans un champ, prêt à être remis dans le formulaire après une erreur
function ancien(string $cle): string
{
    return htmlspecialchars(lireTexte($cle));
}

// ce qu'il faut remettre dans un champ du formulaire : ce qui vient d'être tapé (après une erreur),
// sinon la valeur enregistrée ($defaut)
function valeur(string $cle, string $defaut = ''): string
{
    return htmlspecialchars(isset($_POST[$cle]) ? lireTexte($cle) : $defaut);
}

// ===== Newsletter =====

// le jeton d'un lien de désinscription : il prouve que le lien a été fabriqué par le site pour CET abonné
// (APP_SECRET est dans la configuration ; sans lui, on retombe sur une valeur propre à l'installation)
function jetonDesinscription(int $id, string $email): string
{
    $secret = defined('APP_SECRET') ? APP_SECRET : hash('sha256', RACINE_PATH . DB_NAME . DB_PWD);
    return substr(hash_hmac('sha256', $id . '|' . $email, $secret), 0, 32);
}

// la vérification de l'adresse e-mail est-elle obligatoire pour se connecter ? (EMAIL_VERIFICATION dans la configuration)
function verificationEmailActive(): bool
{
    return defined('EMAIL_VERIFICATION') && EMAIL_VERIFICATION === true;
}

// le jeton du lien de confirmation d'e-mail : il prouve que le lien a été fabriqué par le site pour CE compte
function jetonVerification(int $id, string $email): string
{
    $secret = defined('APP_SECRET') ? APP_SECRET : hash('sha256', RACINE_PATH . DB_NAME . DB_PWD);
    return substr(hash_hmac('sha256', 'verification|' . $id . '|' . $email, $secret), 0, 32);
}

// l'adresse complète du lien de confirmation envoyé à l'inscription
function lienVerification(int $id, string $email): string
{
    return RACINE_URL . 'verification/' . $id . '/' . jetonVerification($id, $email);
}

// l'adresse complète du lien de désinscription d'un abonné
function lienDesinscription(int $id, string $email): string
{
    return RACINE_URL . 'desinscription/' . $id . '/' . jetonDesinscription($id, $email);
}

// le texte d'un e-mail de newsletter : le message de l'administrateur + le lien de désinscription
function texteNewsletter(string $corps, int $id, string $email): string
{
    return $corps . "\n\n--\nVous recevez cet e-mail car vous êtes inscrit(e) à la newsletter de la Maison Rosalie.\n"
        . 'Pour vous désinscrire : ' . lienDesinscription($id, $email) . "\n";
}

// transforme un titre en adresse : "Crème brûlée" donne "creme-brulee"
function creerSlug(string $titre): string
{
    $slug = strtr(mb_strtolower($titre), [
        'à' => 'a',
        'â' => 'a',
        'é' => 'e',
        'è' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'î' => 'i',
        'ï' => 'i',
        'ô' => 'o',
        'ö' => 'o',
        'ù' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ç' => 'c',
        'œ' => 'oe',
    ]);
    // tout ce qui n'est pas une lettre ou un chiffre devient un tiret
    return trim(preg_replace('/[^a-z0-9]+/', '-', $slug), '-');
}

// Transforme le texte "un ingrédient par ligne" en tableau de ['name', 'quantity', 'unit'].
//   "150 g de chocolat noir"  -> quantité 150, unité g,      nom "chocolat noir"
//   "1 pincée de sel"         -> quantité 1,   unité pincée, nom "sel"
//   "4 œufs"                  -> quantité 4,   sans unité,   nom "œufs"
//   "beurre"                  -> sans quantité, sans unité,  nom "beurre"
// Renvoie null s'il y a plus de 40 lignes ou une ligne de plus de 100 caractères.
function lireIngredients(string $texte): ?array
{
    // unité tapée => unité enregistrée (toujours au singulier)
    $unites = [
        'g' => 'g',
        'kg' => 'kg',
        'ml' => 'ml',
        'cl' => 'cl',
        'l' => 'l',
        'pincée' => 'pincée',
        'pincées' => 'pincée',
        'gousse' => 'gousse',
        'gousses' => 'gousse',
        'sachet' => 'sachet',
        'sachets' => 'sachet',
        'cuillère à soupe' => 'cuillère à soupe',
        'cuillères à soupe' => 'cuillère à soupe',
        'cuillère à café' => 'cuillère à café',
        'cuillères à café' => 'cuillère à café',
    ];
    // les unités les plus longues d'abord, pour que "cl" ne soit pas lu "c" puis "l"
    uksort($unites, fn($a, $b) => mb_strlen($b) <=> mb_strlen($a));
    $motifUnites = implode('|', array_map('preg_quote', array_keys($unites)));

    $resultat = [];
    foreach (preg_split('/\R/', $texte) as $ligne) {
        $ligne = trim($ligne);
        if ($ligne === '') {
            continue;
        }
        $quantite = null;
        $unite = null;
        $nom = $ligne;

        // "quantité [unité [de]] nom"
        if (preg_match('/^(\d+(?:[.,]\d+)?|une?)\s*(?:(' . $motifUnites . ')\s+(?:de\s+|d\')?)?(.+)$/iu', $ligne, $m)) {
            $quantite = mb_strtolower($m[1]) === 'un' || mb_strtolower($m[1]) === 'une' ? '1' : str_replace(',', '.', $m[1]);
            $unite = $m[2] === '' ? null : $unites[mb_strtolower($m[2])];
            $nom = trim($m[3]);
            // une quantité de 0 n'a pas de sens (la base la refuse)
            if ((float) $quantite <= 0) {
                $quantite = null;
                $unite = null;
            }
        }
        if (mb_strlen($nom) > 100) {
            return null;
        }
        $resultat[] = ['name' => $nom, 'quantity' => $quantite, 'unit' => $unite];
    }
    return count($resultat) > 40 ? null : $resultat;
}

// les unités de mesure proposées dans la liste déroulante du formulaire (valeur enregistrée => texte affiché)
function unitesIngredient(): array
{
    return [
        'g' => 'g (gramme)',
        'kg' => 'kg (kilo)',
        'ml' => 'ml (millilitre)',
        'cl' => 'cl (centilitre)',
        'l' => 'l (litre)',
        'pincée' => 'pincée',
        'gousse' => 'gousse',
        'sachet' => 'sachet',
        'cuillère à soupe' => 'cuillère à soupe',
        'cuillère à café' => 'cuillère à café',
    ];
}

// les ingrédients du formulaire « Ajouter une recette » : trois listes (quantité, unité, nom), une case par ligne.
// Renvoie null si une ligne est incorrecte : nom vide ou trop long, quantité qui n'est pas un nombre positif,
// unité absente de la liste (le serveur revérifie, même si la liste déroulante a été contournée).
function lireIngredientsListe(): ?array
{
    $noms = $_POST['ing_nom'] ?? [];
    $quantites = $_POST['ing_quantite'] ?? [];
    $unites = $_POST['ing_unite'] ?? [];
    if (!is_array($noms) || !is_array($quantites) || !is_array($unites)) {
        return null;
    }
    $permises = unitesIngredient();
    $resultat = [];
    foreach (array_slice($noms, 0, 41, true) as $i => $nom) {
        $quantite = is_string($quantites[$i] ?? null) ? trim(str_replace(',', '.', $quantites[$i])) : '';
        $unite = is_string($unites[$i] ?? null) ? $unites[$i] : '';
        if (!is_string($nom)) {
            return null;
        }
        $nom = trim(preg_replace('/\s+/u', ' ', $nom));
        if ($nom === '' && $quantite === '') {
            continue; // ligne vide : ignorée
        }
        if ($nom === '' || mb_strlen($nom) > 100) {
            return null;
        }
        if ($quantite === '') {
            if ($unite !== '') {
                return null; // une unité sans quantité n'a pas de sens
            }
            $nombre = null;
        } else {
            if (!is_numeric($quantite) || (float) $quantite <= 0 || (float) $quantite > 100000) {
                return null;
            }
            $nombre = rtrim(rtrim(number_format((float) $quantite, 2, '.', ''), '0'), '.');
        }
        if ($unite !== '' && !isset($permises[$unite])) {
            return null;
        }
        $resultat[] = ['name' => $nom, 'quantity' => $nombre, 'unit' => $unite === '' ? null : $unite];
    }
    return count($resultat) > 40 ? null : $resultat;
}

// Enregistre une image envoyée par un formulaire dans public/assets/.
// $fichier est une entrée de $_FILES. Renvoie ['nom' => nom du fichier ou null, 'erreur' => message ou null].
// Règles du cahier : jpg, png ou webp, 300 Ko maximum.
function enregistrerImage(array $fichier): array
{
    $erreur = $fichier['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($erreur === UPLOAD_ERR_NO_FILE) {
        return ['nom' => null, 'erreur' => null]; // aucun fichier choisi
    }
    if ($erreur !== UPLOAD_ERR_OK || $fichier['size'] > 300 * 1024) {
        return ['nom' => null, 'erreur' => 'L\'image doit faire 300 Ko au maximum.'];
    }
    // le VRAI type est lu dans le fichier : l'extension envoyée par le navigateur peut mentir
    $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $type = (new finfo(FILEINFO_MIME_TYPE))->file($fichier['tmp_name']);
    if (!isset($types[$type]) || @getimagesize($fichier['tmp_name']) === false) {
        return ['nom' => null, 'erreur' => 'L\'image doit être au format jpg, png ou webp.'];
    }
    // le nom est choisi par le serveur, jamais par l'utilisateur
    $nom = 'recette-' . bin2hex(random_bytes(8)) . '.' . $types[$type];
    if (!move_uploaded_file($fichier['tmp_name'], RACINE_PATH . '/public/assets/' . $nom)) {
        return ['nom' => null, 'erreur' => 'L\'image n\'a pas pu être enregistrée.'];
    }
    return ['nom' => $nom, 'erreur' => null];
}

// Répond en JSON et arrête le script. La structure est la même pour toutes les réponses de l'API :
//   { "success": true/false, "message": "...", "data": { ... }, "errors": { champ: "message" } }
// $code est le code HTTP : 200 réussi, 201 créé, 400 données invalides, 401 non connecté,
// 403 interdit, 404 introuvable, 405 mauvaise méthode, 429 trop de demandes, 500 erreur du serveur.
function repondreJson(int $code, bool $succes, string $message, array $donnees = [], array $erreurs = []): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    // (object) : un tableau vide doit devenir {} et pas []
    echo json_encode([
        'success' => $succes,
        'message' => $message,
        'data' => (object) $donnees,
        'errors' => (object) $erreurs,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
// une note sur 5 en étoiles : etoiles(4.3) donne "★★★★☆" (la note est arrondie à l'étoile la plus proche)
function etoiles(?float $note): string
{
    $pleines = (int) round($note ?? 0);
    return str_repeat('★', $pleines) . str_repeat('☆', 5 - $pleines);
}


function adresseDeRetour(): string
{
    $retour = lireTexte('retour');
    $racine = parse_url(RACINE_URL, PHP_URL_PATH);      // "/" ou "/2026/choco1/"
    // que des caractères d'une adresse du site (pas de ":" ni de "\" ni de "//" : pas d'autre site possible)
    if (preg_match('#^[A-Za-z0-9/_.?=&%-]*$#', $retour) !== 1 || str_contains($retour, '//') || !str_starts_with($retour, $racine)) {
        return RACINE_URL;
    }
    $page = substr($retour, strlen($racine));
    // revenir sur la page de connexion n'a pas de sens
    if ($page === '' || str_starts_with($page, 'connection')) {
        return RACINE_URL;
    }
    // sur une recette, on revient directement à la zone « Votre note » / commentaires
    return RACINE_URL . $page . (str_starts_with($page, 'recette/') ? '#votre-note' : '');
}


// ---------- portions : quantités des ingrédients recalculées ----------
// (le même calcul existe en JavaScript dans public/js/portions.js : si tu changes l'un, change l'autre)

// remet de la logique dans les données : "sel 1" devient "1 pincée de sel", "vanille 1" devient "1 gousse de vanille"
// renvoie [quantité, unité, la quantité change-t-elle avec le nombre de personnes ?]
function ingredientLogique(string $nom, ?float $quantite, ?string $unite): array
{
    $unite = $unite === 'gr' ? 'g' : ($unite === '' ? null : $unite);
    $nom = mb_strtolower($nom);
    if ($quantite !== null && $unite === null) {
        if (in_array($nom, ['sel', 'fleur de sel', 'poivre'], true)) {
            $unite = 'pincée';
        } elseif ($nom === 'vanille') {
            $unite = 'gousse';
        }
    }
    // une pincée reste une pincée, quel que soit le nombre de personnes
    return [$quantite, $unite, $quantite !== null && $unite !== 'pincée'];
}

// un nombre à la française : 1.50 donne "1,5" (les cuillères : 1.5 donne "1½")
function nombreFr(float $nombre, bool $fraction = false): string
{
    if ($fraction && fmod($nombre, 1.0) === 0.5) {
        $entier = (int) floor($nombre);
        return ($entier > 0 ? $entier : '') . '½';
    }
    return str_replace('.', ',', rtrim(rtrim(number_format($nombre, 2, '.', ''), '0'), '.'));
}

// la quantité seule, arrondie comme on la lirait dans un livre : "200 g", "1,5 kg", "3", "2 cuillères à soupe"
function quantiteTexte(float $quantite, ?string $unite): string
{
    $unite = $unite === 'gr' ? 'g' : (string) $unite;
    // les grosses quantités changent d'unité : 1200 g = 1,2 kg
    if ($unite === 'g' && $quantite >= 1000) {
        [$quantite, $unite] = [$quantite / 1000, 'kg'];
    } elseif ($unite === 'ml' && $quantite >= 1000) {
        [$quantite, $unite] = [$quantite / 1000, 'l'];
    } elseif ($unite === 'cl' && $quantite >= 100) {
        [$quantite, $unite] = [$quantite / 100, 'l'];
    }

    $fraction = false;
    if ($unite === 'g' || $unite === 'ml') {
        // 333 g devient 335 g : on pèse à 5 g près
        $quantite = $quantite < 10 ? round($quantite, 1) : round($quantite / 5) * 5;
    } elseif ($unite === 'kg' || $unite === 'l' || $unite === 'cl') {
        $quantite = round($quantite, 2);
    } elseif (str_starts_with($unite, 'cuillère')) {
        // une demi-cuillère existe, pas un tiers
        $quantite = max(0.5, floor($quantite * 2 + 0.5) / 2);
        $fraction = true;
    } else {
        // un œuf, une gousse, un sachet, une pincée : toujours entiers, au moins 1
        $quantite = max(1, floor($quantite + 0.5));
    }

    // pluriel des unités "comptées" : 2 pincées, 3 cuillères à soupe
    if ($quantite >= 2 && in_array($unite, ['pincée', 'gousse', 'sachet', 'cuillère à soupe', 'cuillère à café'], true)) {
        $unite = str_replace('cuillère', 'cuillères', $unite);
        $unite = in_array($unite, ['pincée', 'gousse', 'sachet'], true) ? $unite . 's' : $unite;
    }
    return trim(nombreFr($quantite, $fraction) . ' ' . $unite);
}

// la phrase complète : "200 g de chocolat noir", "100 g d'amandes", "3 œufs", "1 pincée de sel"
function libelleIngredient(string $nom, ?float $quantite, ?string $unite): string
{
    [$quantite, $unite] = ingredientLogique($nom, $quantite, $unite);
    if ($quantite === null) {
        return $nom;
    }
    $texte = quantiteTexte($quantite, $unite);
    if ($unite !== null) {
        $de = preg_match('/^[aeiouyhéèêàâîïôœ]/iu', $nom) === 1 ? " d'" : ' de ';
        return $texte . $de . $nom;
    }
    // sans unité : "3 œuf" devient "3 œufs"
    if (max(1, floor($quantite + 0.5)) >= 2 && !preg_match('/[sxz]$/u', $nom)) {
        $nom .= 's';
    }
    return $texte . ' ' . $nom;
}

// le texte d'une étape : {chocolat noir} devient "200 g de chocolat noir" (en <span> pour que le JavaScript le recalcule)
function texteEtape(string $description, array $ingredients): string
{
    $parNom = [];
    foreach ($ingredients as $ingredient) {
        $parNom[mb_strtolower($ingredient->getName())] = $ingredient;
    }

    return preg_replace_callback('/\{([^{}]+)\}/u', function (array $m) use ($parNom): string {
        $nom = trim(htmlspecialchars_decode($m[1]));
        $cle = mb_strtolower($nom);
        // {œuf} retrouve aussi l'ingrédient "œufs", et l'inverse
        $ingredient = $parNom[$cle] ?? $parNom[$cle . 's'] ?? $parNom[rtrim($cle, 's')] ?? null;
        if ($ingredient === null || $ingredient->getQuantity() === null) {
            return htmlspecialchars($nom);
        }
        [$quantite, $unite, $change] = ingredientLogique($ingredient->getName(), (float) $ingredient->getQuantity(), $ingredient->getUnit());
        $texte = htmlspecialchars(libelleIngredient($ingredient->getName(), $quantite, $unite));
        if (!$change) {
            return $texte; // "1 pincée de sel" ne bouge pas
        }
        return '<span class="qte" data-nom="' . htmlspecialchars($ingredient->getName()) . '" data-qte="' . $quantite
            . '" data-unite="' . htmlspecialchars((string) $unite) . '">' . $texte . '</span>';
    }, htmlspecialchars($description));
}
