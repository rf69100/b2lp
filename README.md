![Logo LyonPalme](logo_lp.png)

# Application blog de Lyon Palme "**B2LP**"

Code du webservice développé avec Laravel. Ce webservice sera interrogé par l'application client leger "**b2LP**" développée avec ReactNative.

Mise à jour  _Mars 2025_.

### 1. Programmation.

- Développement avec le framework **Laravel**.
- Mise en place d'un système de **logs**.
- Utilisation du système d'authentification **Sanctum**  par Bearer Token.

### 2. Installation sur la VM WSL.

- Créer la base de données et le user sur Mariadb,
- Cloner le projet dans websites/laravel,
- Donner les droits aux répertoires _boostrap/cache_ et _storage_,
- Dupliquer le fichier _.env.example_, le renommer _.env_,
- Paramétrer l'accès à la db,
- Faire `composer install`,
- Dans vscode :
    * faire `Artisan: key generation`,
    * faire `Artisan: migrate install`,
    * faire `Artisan: migrate`.
- Pour peupler la db :
    * créer des users avec l'api : voir endpoint ci-dessous,
    * faire `php artisan db:seed --class=BilletSeeder`,
    * faire `php artisan db:seed --class=UserSeeder`,
    * faire `php artisan db:seed --class=CommentaireSeeder`.

- _Pour la mise en production_ :
    * créer un user dans la DB avec des droits CRUD uniquement,
    * [suivre les instructions de laravel.](https://laravel.com/docs/11.x/deployment)
    

### 3. API Endpoints.

#### Authentification

| **Nom** | **Méthode** | **Url** | **Response Code** |
| ------- | ----------- | ------- | ----------------- | 
| Register | `POST` | _api/register_ | `200`, `404`, `500` |

**Création du compte d'un utilisateur**.

- Data Received
    - `name` (string) : nom de l'utilisateur.
    - `mail` (string) : email de l'utilisateur.
    - `password` (string) : Mot de passe.
- Data Send
    - `auth_token` (cookie) : Bearer Token pour l'authentification.

| **Nom** | **Méthode** | **Url** | **Response Code** |
| ------- | ----------- | ------- | ----------------- | 
| Login | `POST` | _api/login_ | `200`, `422`, `404` |

**Connexion d'un utilisateur**.

- Data Received
    - `mail` (string) : mail de l'utilisateur.
    - `password` (string) : Mot de passe.
- Data Send
    - `auth_token` (cookie) : Bearer Token pour l'authentification.

| **Nom** | **Méthode** | **Url** | **Response Code** |
| ------- | ----------- | ------- | ----------------- | 
| Logout | `POST` | _api/user/logout_ | `200` |

**Déconnexion d'un utilisateur**.

- Data Received
    - `auth_token` (cookie) : Bearer Token pour l'authentification.
- Data Send
    - `None`.

| **Nom** | **Méthode** | **Url** | **Response Code** |
| ------- | ----------- | ------- | ----------------- | 
| User | `GET` | _api/user_ | `200`, `401` |

**Vérification de la connexion d'un utilisateur**.

- Data Received
    - `auth_token` (cookie) : Bearer Token pour l'authentification.
- Data Send
    - `id`,
    - `nom`,
    - `email`.

#### Application Blog : consultation des billets.

| **Nom** | **Méthode** | **Url** | **Response Code** |
| ------- | ----------- | ------- | ----------------- | 
| Billets | `GET` | _api/billets_ | `200`, `500` |

**Listing de tous les billets**. Affichage réduit : titre, date, contenu.
_Pas d'authentification requise_.

- Data Received
    - `None`.
- Data Send
    - `billets complet` (array) : les billets.

| **Nom** | **Méthode** | **Url** | **Response Code** |
| ------- | ----------- | ------- | ----------------- | 
| BilletById | `GET` | _api/billets/{id}_ | `200`, `500`|

**Affiche le détail d'un billet d'id {id}**.

- Data Received
    - `auth_token` (cookie) : Bearer Token pour l'authentification.
    - `{id}`
- Data Send
    - `billet` (array) : Le détail du billet sélectionné avec ses commentaires et les noms des auteurs des commentaires. 

#### Application Blog : insertion en base.

| **Nom** | **Méthode** | **Url** | **Response Code** |
| ------- | ----------- | ------- | ----------------- | 
| StoreCommentaire | `POST` | _api/commentaires_ | `201`, `422`, `500` |

**Insertion d'un commentaire dans la base de données**. Authentification requise.

- Data Received (JSON body)
    - `COM_CONTENU` (string, max 200) : message de l'auteur,
    - `billet_id` (integer) : id du billet auquel correspond le commentaire,
    - `user_id` (integer) : id de l'auteur — doit correspondre à l'utilisateur authentifié.
- Data Send
    - `Date` : date de création (définie côté serveur),
    - `Auteur` : nom de l'auteur,
    - `Contenu` : message.

---

## Fonctionnalité : catégorisation des billets

_Ajout : Juin 2026._

Un billet peut désormais appartenir à **une ou plusieurs catégories** (ex : `monopalme`, `bi-palmes`, `compétition`, `sécurité`, `randonnée palmée`), et une catégorie peut concerner plusieurs billets. C'est une relation **plusieurs-à-plusieurs**. Cette section détaille toutes les modifications apportées.

### 1. Migrations

Deux nouvelles migrations (`database/migrations/`) :

- **`..._create_categories_table.php`** — table `categories` :
    - `id` (clé primaire),
    - `CAT_LIBELLE` (string) : le libellé de la catégorie,
    - `timestamps`.
- **`..._create_billet_categorie_table.php`** — table pivot `billet_categorie` (lie billets et catégories) :
    - `id`,
    - `billet_id` (clé étrangère vers `billets`, `onDelete cascade`),
    - `categorie_id` (clé étrangère vers `categories`, `onDelete cascade`),
    - contrainte d'unicité sur le couple (`billet_id`, `categorie_id`) pour éviter les doublons,
    - `timestamps`.

> ⚠️ La migration du pivot porte un timestamp **postérieur** à celle des catégories : la table `categories` doit exister avant que le pivot ne crée sa clé étrangère.

### 2. Modèles

- **`app/Models/Categorie.php`** (nouveau) :
    - `$fillable` : `CAT_LIBELLE`,
    - relation `billets()` (`belongsToMany`) vers `Billet` via la table pivot.
- **`app/Models/Billet.php`** (modifié) :
    - relation `categories()` (`belongsToMany`) vers `Categorie` via la table pivot,
    - **accesseur** `libelles_categories` : `$billet->libelles_categories` renvoie le tableau des libellés de toutes les catégories du billet.

### 3. Contrôleur

**`app/Http/Controllers/BilletController.php`** (modifié) :

- `index()` :
    - charge les catégories avec les billets (**eager loading**, évite les requêtes N+1),
    - accepte un **filtre optionnel** `?categorie_id=X` pour ne retourner que les billets d'une catégorie donnée.
- `show()` : charge également les catégories du billet en plus de ses commentaires.

### 4. Resources

- **`app/Http/Resources/CategorieResource.php`** (nouveau) : formate une catégorie en `{ "Id", "Libelle" }`.
- **`BilletsResource`** et **`BilletResource`** (modifiés) : chaque billet expose désormais un champ `Categories` (tableau de `CategorieResource`), aussi bien dans la liste que dans le détail.

### 5. Peuplement (seeder)

**`database/seeders/CategorieSeeder.php`** (nouveau) : crée les 5 catégories de base et rattache 1 à 3 catégories aléatoires à chaque billet existant.

À lancer **après** le `BilletSeeder` :

```bash
php artisan db:seed --class=CategorieSeeder
```

### 6. Tests

**`tests/Feature/BilletCategorieTest.php`** (nouveau) : 6 tests couvrant la relation dans les deux sens, l'accesseur, l'exposition des catégories en liste et en détail, et le filtrage.

Les tests tournent sur une base dédiée **`monblog_test`** (configurée dans `phpunit.xml` via `DB_DATABASE`) afin de ne jamais toucher aux données de développement.

```bash
php artisan test --filter=BilletCategorieTest
```

### 7. Endpoints impactés

| **Nom** | **Méthode** | **Url** | **Response Code** |
| ------- | ----------- | ------- | ----------------- |
| Billets (filtrables) | `GET` | _api/billets_ et _api/billets?categorie\_id={id}_ | `200`, `500` |

**Listing des billets, avec leurs catégories**. Le paramètre optionnel `categorie_id` filtre les billets d'une catégorie donnée. _Pas d'authentification requise_.

- Data Received
    - `categorie_id` (integer, optionnel) : id de la catégorie pour filtrer.
- Data Send (pour chaque billet)
    - `Date`, `Titre`, `Contenu`,
    - `Categories` (array) : liste `{ "Id", "Libelle" }` des catégories du billet.

Le détail d'un billet (`GET /api/billets/{id}`) renvoie lui aussi le champ `Categories` en plus de ses commentaires.
