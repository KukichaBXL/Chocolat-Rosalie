# API de Maison Rosalie

Les adresses `/api/...` répondent **toujours en JSON**. Elles sont appelées par le JavaScript du site (`fetch`).

## Format de toutes les réponses

```json
{ "success": true, "message": "Texte affichable", "data": { }, "errors": { } }
```

| Champ | Contenu |
|---|---|
| `success` | `true` si l'action a réussi, sinon `false` |
| `message` | une phrase lisible par le visiteur |
| `data` | les données utiles (`{}` s'il n'y en a pas) |
| `errors` | une erreur par champ : `{ "rate": "message" }` (`{}` s'il n'y en a pas) |

## Codes HTTP

| Code | Signification |
|---|---|
| 200 | réussi |
| 201 | créé |
| 400 | données invalides |
| 401 | non connecté |
| 403 | interdit (jeton CSRF invalide, pas le bon rôle) |
| 404 | introuvable |
| 405 | mauvaise méthode HTTP |
| 429 | trop de demandes |
| 500 | erreur du serveur (le détail est dans `logs/erreurs.log`, jamais à l'écran) |

## Règles communes

- Toute requête **POST** envoie le champ `csrf_token` (le jeton de la session). Sans lui : 403.
- Le serveur **ne fait jamais confiance au navigateur** : la connexion, le rôle et chaque valeur sont revérifiés à chaque requête.

---

## POST `/api/noter`

Donne une note à une recette. Une seule note par utilisateur et par recette : noter à nouveau **remplace** l'ancienne note.

- **Accès** : utilisateur connecté.
- **Champs envoyés** (formulaire) :

| Champ | Règle |
|---|---|
| `recipe_id` | numéro d'une recette existante |
| `rate` | nombre entier de 1 à 5 |
| `csrf_token` | jeton de la session |

- **Réponses** :

| Code | Quand | Contenu |
|---|---|---|
| 200 | note enregistrée | `data: { "rate": 4, "average": 4.3, "votes": 7 }` |
| 400 | `rate` ou `recipe_id` invalide (0, 6, texte...) | `errors: { "rate": "..." }` |
| 401 | personne n'est connecté | |
| 403 | jeton CSRF invalide | |
| 404 | la recette n'existe pas | |
| 405 | autre méthode que POST | |

---

## GET `/api/commentaires?recipe_id=1&offset=10`

Charge 10 commentaires de plus (« Voir plus d'avis »), du plus récent au plus ancien, à partir de `offset`. Les commentaires masqués par l'administrateur ne sont jamais envoyés.

- **Accès** : tout le monde.

| Paramètre | Règle |
|---|---|
| `recipe_id` | numéro d'une recette existante |
| `offset` | nombre de commentaires déjà affichés (0 par défaut) |

| Code | Quand | Contenu |
|---|---|---|
| 200 | réussi | `data: { "comments": [ { "id", "author", "authorId", "date", "rate", "title", "text", "canDelete", "deleteLabel" } ], "hasMore": true }` |
| 400 | `recipe_id` ou `offset` invalide | |
| 404 | la recette n'existe pas | |
| 405 | autre méthode que GET | |

`canDelete` vaut `true` si la personne connectée est l'auteur du commentaire ou un administrateur.
`rate` est la note (1 à 5) que l'auteur a donnée à la recette, ou `null` s'il n'a pas noté (on peut noter sans commenter).

## POST `/api/commenter`

Ajoute un commentaire. Limite : **3 commentaires par tranche de 5 minutes et par utilisateur**.

- **Accès** : utilisateur connecté.

| Champ | Règle |
|---|---|
| `recipe_id` | numéro d'une recette existante |
| `title` | le sujet, facultatif, 120 caractères maximum |
| `text` | le message, de 3 à 500 caractères (espaces au début et à la fin retirés) |
| `csrf_token` | jeton de la session |

| Code | Quand | Contenu |
|---|---|---|
| 201 | commentaire créé | `data: { "comment": { "id", "author", "authorId", "date", "rate", "title", "text", "canDelete", "deleteLabel" } }` |
| 400 | champ invalide | `errors: { "text": "..." }` |
| 401 | personne n'est connecté | |
| 403 | jeton CSRF invalide | |
| 404 | la recette n'existe pas | |
| 405 | autre méthode que POST | |
| 429 | déjà 3 commentaires en 5 minutes | |

Le texte est renvoyé tel quel (le HTML n'est pas retiré) : c'est le JavaScript qui l'affiche avec `textContent`, donc il ne s'exécute jamais.

## POST `/api/supprimer-commentaire`

Supprime un commentaire. **L'auteur** efface vraiment son propre commentaire ; **l'administrateur** ne fait que le **masquer** (statut `banni`, il reste dans la base et peut être réaffiché dans l'admin).

- **Accès** : l'auteur du commentaire, ou un administrateur.

| Champ | Règle |
|---|---|
| `comment_id` | numéro d'un commentaire existant |
| `csrf_token` | jeton de la session |

| Code | Quand | Contenu |
|---|---|---|
| 200 | supprimé (par son auteur) ou masqué (par l'administrateur) | `data: { "id": 12 }` |
| 400 | `comment_id` invalide | `errors: { "comment_id": "..." }` |
| 401 | personne n'est connecté | |
| 403 | jeton CSRF invalide, ou commentaire d'un autre utilisateur | |
| 404 | le commentaire n'existe pas | |
| 405 | autre méthode que POST | |
