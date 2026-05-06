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
