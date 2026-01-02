# Laravel Activity Tracker - Instructions de développement

## Description du projet

**Laravel Activity Tracker** est un package de journalisation d'activités pour Laravel. Il enregistre automatiquement les actions des utilisateurs (authentifiés, invités, robots) avec des détails complets : IP, navigateur, route, méthode HTTP, etc.

## Structure du projet

```
src/
├── LaravelActivityTrackerServiceProvider.php    # Point d'entrée du package
├── config/laravel-activity-tracker.php          # Configuration complète
├── App/
│   ├── Http/
│   │   ├── Controllers/ActivityTrackerController.php
│   │   ├── Middleware/LogActivity.php
│   │   └── Traits/
│   │       ├── ActivityLogger.php      # Trait principal de logging
│   │       ├── IpAddressDetails.php    # Géolocalisation IP
│   │       └── UserAgentDetails.php    # Parsing User-Agent
│   ├── Listeners/                      # Événements d'authentification (7 fichiers)
│   ├── Models/Activity.php             # Modèle Eloquent avec SoftDeletes
│   └── Logic/helpers.php
├── database/migrations/
├── resources/
│   ├── lang/{en,fr,de,pt-br,tr}/       # Traductions
│   ├── views/                          # Vues Blade
│   └── routes/web.php
```

## Commandes utiles

```bash
# Publication des assets
php artisan vendor:publish --tag=LaravelActivityTracker

# Publication sélective
php artisan vendor:publish --tag=laravelactivitytracker-config
php artisan vendor:publish --tag=laravelactivitytracker-views
php artisan vendor:publish --tag=laravelactivitytracker-lang
php artisan vendor:publish --tag=laravelactivitytracker-migrations

# Migrations
php artisan migrate
```

## Architecture technique

### Modèle Activity

Table: `laravel_activity_tracker`

| Colonne     | Type              | Description                          |
|-------------|-------------------|--------------------------------------|
| description | longText          | Description de l'action              |
| details     | longText          | Détails additionnels (nullable)      |
| userType    | string            | guest / registered / crawler         |
| userId      | integer           | ID utilisateur (nullable)            |
| route       | longText          | URL de la requête                    |
| ipAddress   | ipAddress         | Adresse IP du client                 |
| userAgent   | text              | User-Agent du navigateur             |
| locale      | string            | Langue du client                     |
| referer     | longText          | Page d'origine                       |
| methodType  | string            | GET, POST, PUT, DELETE, etc.         |
| relId       | unsignedBigInteger| ID du modèle lié (nullable, indexé)  |
| relModel    | string            | Classe du modèle lié (nullable)      |

### Trait ActivityLogger

Méthode principale pour logger une activité :

```php
use Dimer47\LaravelActivityTracker\App\Http\Traits\ActivityLogger;

// Dans un contrôleur ou service
$this->activity($description);
$this->activity($description, $details);
$this->activity($description, $details, ['id' => $model->id, 'model' => Model::class]);
```

### Routes du package

| Route                       | Méthode | Description                     |
|-----------------------------|---------|----------------------------------|
| /activity                   | GET     | Tableau de bord principal        |
| /activity/cleared           | GET     | Logs supprimés (soft deleted)    |
| /activity/log/{id}          | GET     | Détail d'une activité            |
| /activity/clear-activity    | DELETE  | Soft delete des logs             |
| /activity/destroy-activity  | DELETE  | Suppression définitive           |
| /activity/restore-log       | POST    | Restaurer les logs               |
| /activity/live-search       | POST    | Recherche en temps réel          |

## Configuration importante

Variables d'environnement clés dans `.env` :

```env
# Base de données
LARAVEL_ACTIVITY_TRACKER_DATABASE_CONNECTION=mysql
LARAVEL_ACTIVITY_TRACKER_DATABASE_TABLE=laravel_activity_tracker

# Middleware
LARAVEL_ACTIVITY_TRACKER_MIDDLEWARE_ENABLED=true
LARAVEL_ACTIVITY_TRACKER_MIDDLEWARE_EXCEPT=

# Sécurité (intégration avec packages de rôles)
LARAVEL_ACTIVITY_TRACKER_ROLES_ENABLED=false
LARAVEL_ACTIVITY_TRACKER_ROLES_MIDDLWARE=role:admin

# Pagination
LARAVEL_ACTIVITY_TRACKER_PAGINATION_ENABLED=true
LARAVEL_ACTIVITY_TRACKER_CURSOR_PAGINATION_ENABLED=false  # Pour gros volumes
LARAVEL_ACTIVITY_TRACKER_PAGINATION_PER_PAGE=25

# Interface
LARAVEL_ACTIVITY_TRACKER_LAYOUT=layouts.app
LARAVEL_ACTIVITY_TRACKER_BOOTSTRAP_VERSION=4
LARAVEL_ACTIVITY_TRACKER_ENABLE_SEARCH=false
```

## Conventions de code

### Namespace

```php
namespace Dimer47\LaravelActivityTracker;
```

### Validation des données

Toujours utiliser les règles définies dans `Activity::rules()` avant insertion. Le modèle valide automatiquement :
- `description` : requis, string
- `userType` : requis, parmi les types valides
- `route` : URL valide ou null
- `ipAddress` : IP valide ou null

### Gestion des IP derrière proxy

Le trait `IpAddressDetails` gère automatiquement :
- Cloudflare : `HTTP_CF_CONNECTING_IP`
- Proxies : `HTTP_X_FORWARDED_FOR`
- IP directe : `REMOTE_ADDR`

### Types d'utilisateurs

Les types sont définis dans les fichiers de langue :
- `guest` : Visiteur non authentifié
- `registered` : Utilisateur connecté
- `crawler` : Robot/bot détecté via `jaybizzle/laravel-crawler-detect`

## Tests

Pour tester le logging :

```php
// Via middleware (automatique)
Route::middleware(['activity'])->group(function () {
    // Routes loggées automatiquement
});

// Via trait (manuel)
$this->activity('Action de test', json_encode(['key' => 'value']));
```

## Points d'attention

1. **Performance** : Utiliser la pagination par curseur (`LARAVEL_ACTIVITY_TRACKER_CURSOR_PAGINATION_ENABLED=true`) pour les tables avec millions d'enregistrements.

2. **Soft Deletes** : Le modèle utilise `SoftDeletes`. Les logs "supprimés" restent en base et sont accessibles via `/activity/cleared`.

3. **Géolocalisation** : L'API GeoPlugin est appelée pour chaque IP. Considérer la mise en cache en production.

4. **Modèles liés** : Utiliser `relId` et `relModel` pour lier une activité à une entité spécifique (utile pour l'audit).

## Dépendances

- `jaybizzle/laravel-crawler-detect` : Détection des bots
- `laravelcollective/html` : Formulaires HTML

## Langues supportées

- Anglais (en)
- Français (fr)
- Allemand (de)
- Portugais brésilien (pt-br)
- Turc (tr)
