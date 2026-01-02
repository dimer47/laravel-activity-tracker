# Laravel Activity Tracker - Nouvelles fonctionnalités

## 📅 Filtrage par date

### Description

Ajout du filtrage par plage de dates pour une meilleure gestion des activités.

### Utilisation

#### 1. Filtrage par plage de dates

```php
// Dans le contrôleur
$activities = Activity::whereDate('created_at', '>=', '2024-01-01')
                     ->whereDate('created_at', '<=', '2024-12-31')
                     ->get();
```

#### 2. Périodes prédéfinies

- `today` - Aujourd'hui
- `yesterday` - Hier
- `last_7_days` - 7 derniers jours
- `last_30_days` - 30 derniers jours
- `last_3_months` - 3 derniers mois
- `last_6_months` - 6 derniers mois
- `last_year` - Dernière année

#### 3. Paramètres URL

```
/activity?date_from=2024-01-01&date_to=2024-12-31
/activity?period=last_7_days
```

### Configuration

```php
// config/laravel-activity-tracker.php
'enableDateFiltering' => env('LARAVEL_ACTIVITY_TRACKER_ENABLE_DATE_FILTERING', true),
```

## 📊 Fonctionnalité d'export

### Description

Ajout de la fonctionnalité d'export des activités dans différents formats.

### Formats supportés

#### 1. Export CSV

```php
// URL
/activity/export?format=csv

// Méthode
public function exportToCsv($activities)
{
    $filename = 'activity_log_' . now()->format('Y-m-d_H-i-s') . '.csv';
    // ... implémentation
}
```

#### 2. Export JSON

```php
// URL
/activity/export?format=json

// Méthode
public function exportToJson($activities)
{
    $filename = 'activity_log_' . now()->format('Y-m-d_H-i-s') . '.json';
    // ... implémentation
}
```

#### 3. Export Excel

```php
// URL
/activity/export?format=excel

// Méthode
public function exportToExcel($activities)
{
    $filename = 'activity_log_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
    // ... implémentation
}
```

### Utilisation avec filtres

```php
// Export avec filtres
/activity/export?format=csv&date_from=2024-01-01&date_to=2024-12-31
/activity/export?format=json&period=last_7_days&user=123
```

### Configuration

```php
// config/laravel-activity-tracker.php
'enableExport' => env('LARAVEL_ACTIVITY_TRACKER_ENABLE_EXPORT', true),
```

## 🎨 Intégration Frontend

### Exemple de template Blade

```blade
{{-- Ajouter dans activity-log.blade.php --}}
@include('LaravelActivityTracker::partials.filter-export-form')
```

### JavaScript pour filtrage dynamique

```javascript
// Ajouter dans scripts.blade.php
document.getElementById("period").addEventListener("change", function () {
  if (this.value) {
    document.getElementById("date_from").value = "";
    document.getElementById("date_to").value = "";
  }
});
```

## 🔧 Utilisation API

### Export via API

```php
// Dans le contrôleur
public function exportActivityLog(Request $request)
{
    $format = $request->get('format', 'csv');
    $activities = Activity::orderBy('created_at', 'desc');

    // Appliquer les filtres
    if (config('LaravelActivityTracker.enableDateFiltering')) {
        $activities = $this->applyDateFilter($activities, $request);
    }

    // Export
    switch ($format) {
        case 'csv':
            return $this->exportToCsv($activities->get());
        case 'json':
            return $this->exportToJson($activities->get());
        case 'excel':
            return $this->exportToExcel($activities->get());
    }
}
```

## 📝 Routes

### Nouvelles routes

```php
// routes/web.php
Route::get('/export', ['uses' => 'ActivityTrackerController@exportActivityLog'])->name('export-activity');
```

## 🌐 Traductions

### Français (fr)

```php
// resources/lang/fr/laravel-activity-tracker.php
'filterAndExport' => 'Filtrer et Exporter',
'fromDate' => 'Date de début',
'toDate' => 'Date de fin',
'exportCSV' => 'Exporter CSV',
'exportJSON' => 'Exporter JSON',
'exportExcel' => 'Exporter Excel',
// ... plus de traductions
```

## 🚀 Installation et utilisation

### 1. Installer le package

```bash
composer require dimer47/laravel-activity-tracker
```

### 2. Publier les configurations

```bash
php artisan vendor:publish --provider="Dimer47\LaravelActivityTracker\LaravelActivityTrackerServiceProvider"
```

### 3. Ajouter les routes

```php
// routes/web.php
Route::group(['middleware' => ['web', 'auth']], function () {
    // Ajouter les routes Laravel Activity Tracker
});
```

### 4. Utiliser dans Blade

```blade
{{-- Dans votre layout --}}
@include('LaravelActivityTracker::partials.filter-export-form')
```

## 🔍 Exemple d'utilisation

### Filtrage et export

```php
// Dans le contrôleur
public function getFilteredActivities(Request $request)
{
    $activities = Activity::orderBy('created_at', 'desc');

    // Appliquer les filtres
    if ($request->filled('date_from')) {
        $activities->whereDate('created_at', '>=', $request->get('date_from'));
    }

    if ($request->filled('period')) {
        switch ($request->get('period')) {
            case 'last_7_days':
                $activities->where('created_at', '>=', now()->subDays(7));
                break;
            // ... plus de cas
        }
    }

    return $activities->get();
}
```

## 📊 Statistiques

### Nombre d'activités par période

```php
$todayCount = Activity::whereDate('created_at', today())->count();
$weekCount = Activity::where('created_at', '>=', now()->subDays(7))->count();
$monthCount = Activity::where('created_at', '>=', now()->subDays(30))->count();
```

## 🎯 Avantages

1. **Performance améliorée** - Filtrage au niveau de la base de données
2. **Flexibilité** - Support de différents formats d'export
3. **Expérience utilisateur** - Interface intuitive pour le filtrage
4. **Scalabilité** - Gestion efficace de grandes quantités de données
5. **Intégration** - Intégration facile avec les systèmes existants
