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
