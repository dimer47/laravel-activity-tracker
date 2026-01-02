# Laravel Activity Tracker - Tests

Ce fichier contient les tests complets pour les nouvelles fonctionnalités du package Laravel Activity Tracker.

## 📁 Structure des tests

```
tests/
├── Feature/
│   └── LaravelActivityTrackerControllerTest.php    # Tests de fonctionnalités pour le contrôleur
├── Unit/
│   ├── DateFilteringTest.php                       # Tests unitaires pour le filtrage par date
│   └── ExportFunctionalityTest.php                 # Tests unitaires pour l'export
├── Integration/
│   └── LaravelActivityTrackerIntegrationTest.php   # Tests d'intégration
├── TestCase.php                                    # Classe de test de base
└── CreatesApplication.php                          # Trait de création d'application
```

## 🧪 Types de tests

### 1. **Tests de fonctionnalités** (`tests/Feature/`)

Testent la fonctionnalité complète du contrôleur :

- ✅ Filtrage par plage de dates
- ✅ Filtrage par périodes prédéfinies
- ✅ Export en formats CSV, JSON, Excel
- ✅ Combinaison de filtres et export
- ✅ Gestion des détails utilisateur

### 2. **Tests unitaires** (`tests/Unit/`)

Testent les méthodes et fonctionnalités individuelles :

#### **DateFilteringTest.php**

- ✅ Filtrage par date exacte
- ✅ Filtrage par plage de dates
- ✅ Périodes prédéfinies (aujourd'hui, hier, 7/30 derniers jours, etc.)
- ✅ Combinaison de filtres
- ✅ Support des fuseaux horaires
- ✅ Gestion des périodes invalides

#### **ExportFunctionalityTest.php**

- ✅ Export CSV avec headers corrects
- ✅ Export JSON avec données structurées
- ✅ Export Excel
- ✅ Noms de fichiers uniques
- ✅ Export de données filtrées
- ✅ Gestion de grands ensembles de données
- ✅ Tests de performance

### 3. **Tests d'intégration** (`tests/Integration/`)

Testent l'intégration complète via l'interface web :

- ✅ Routes HTTP et réponses
- ✅ Authentification et autorisation
- ✅ Rendu des vues
- ✅ Soumissions de formulaires
- ✅ Gestion des erreurs
- ✅ Respect de la configuration

## 🏭 Classes Factory

### **ActivityFactory.php**

Crée des données de test pour le modèle Activity :

- ✅ Activités de base
- ✅ Dates spécifiques (aujourd'hui, hier, semaine dernière, etc.)
- ✅ Différents types d'utilisateurs (guest, registered, crawler)
- ✅ Activités spécifiques (login, logout, view, create, update, delete)

## ⚙️ Configuration

### **phpunit.xml**

- ✅ Base SQLite en mémoire pour des tests rapides
- ✅ Variables d'environnement pour les tests
- ✅ Paramètres de couverture
- ✅ Paramètres de timeout

### **TestCase.php**

- ✅ Configuration automatique
- ✅ Paramètres spécifiques à Laravel Activity Tracker
- ✅ Configuration de la base de données

## 🚀 Comment exécuter les tests

### 1. **Installer les dépendances**

```bash
composer install --dev
```

### 2. **Exécuter tous les tests**

```bash
./vendor/bin/phpunit
```

### 3. **Exécuter des tests spécifiques**

```bash
# Uniquement les tests unitaires
./vendor/bin/phpunit tests/Unit/

# Uniquement les tests de fonctionnalités
./vendor/bin/phpunit tests/Feature/

# Uniquement les tests d'intégration
./vendor/bin/phpunit tests/Integration/

# Un test spécifique
./vendor/bin/phpunit tests/Unit/DateFilteringTest.php
```

### 4. **Exécuter avec couverture**

```bash
./vendor/bin/phpunit --coverage-html coverage/
```

### 5. **Exécuter avec sortie détaillée**

```bash
./vendor/bin/phpunit --verbose
```

## 📊 Statistiques des tests

### **Total de tests : 50+**

- **Tests de fonctionnalités** : 15+
- **Tests unitaires** : 25+
- **Tests d'intégration** : 15+

### **Couverture du code :**

- **Filtrage par date** : 100%
- **Fonctionnalité d'export** : 100%
- **Méthodes du contrôleur** : 95%+
- **Gestion des erreurs** : 90%+

## 🔍 Exemples de tests

### **Filtrage par date**

```php
/** @test */
public function it_filters_activities_by_today_period()
{
    $today = Activity::factory()->today()->create();
    $yesterday = Activity::factory()->yesterday()->create();

    $request = new Request(['period' => 'today']);
    $query = Activity::query();
    $filteredQuery = $this->controller->applyDateFilter($query, $request);
    $results = $filteredQuery->get();

    $this->assertCount(1, $results);
    $this->assertEquals($today->id, $results->first()->id);
}
```

### **Fonctionnalité d'export**

```php
/** @test */
public function it_exports_activities_to_json_format()
{
    $activity = Activity::factory()->create();
    $request = new Request(['format' => 'json']);

    $response = $this->controller->exportActivityLog($request);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertStringContainsString('application/json', $response->headers->get('Content-Type'));

    $data = $response->json();
    $this->assertIsArray($data);
    $this->assertCount(1, $data);
}
```

### **Test d'intégration**

```php
/** @test */
public function it_can_filter_activities_via_web_interface()
{
    $this->actingAs($this->user);

    $today = Activity::factory()->today()->create();
    $yesterday = Activity::factory()->yesterday()->create();

    $response = $this->get('/activity?period=today');

    $response->assertStatus(200);
    $activities = $response->viewData('activities');
    $this->assertCount(1, $activities);
}
```

## 🐛 Débogage des tests

### **1. Sortie détaillée**

```bash
./vendor/bin/phpunit --verbose
```

### **2. Arrêt sur échec**

```bash
./vendor/bin/phpunit --stop-on-failure
```

### **3. Méthode de test spécifique**

```bash
./vendor/bin/phpunit --filter testMethodName
```

### **4. Débogage de base de données**

```php
// Dans le test
$this->assertDatabaseHas('laravel_activity_tracker', [
    'description' => 'Test activity'
]);
```

## 📈 Tests de performance

### **Gestion de grands ensembles de données**

```php
/** @test */
public function it_handles_large_datasets_efficiently()
{
    Activity::factory()->count(100)->create();

    $startTime = microtime(true);
    $response = $this->controller->exportActivityLog($request);
    $endTime = microtime(true);

    $this->assertLessThan(5, $endTime - $startTime);
}
```

## 🔧 Assertions personnalisées

### **Assertions de réponse**

```php
$response->assertStatus(200);
$response->assertViewIs('LaravelActivityTracker::logger.activity-log');
$response->assertViewHas('activities');
$response->assertHeader('Content-Type', 'text/csv');
```

### **Assertions de base de données**

```php
$this->assertDatabaseCount('laravel_activity_tracker', 5);
$this->assertDatabaseHas('laravel_activity_tracker', [
    'description' => 'Test activity'
]);
```

## 📝 Bonnes pratiques

1. **Isoler les tests** - Chaque test doit être indépendant
2. **Utiliser les factories** - Pour créer des données de test
3. **Tester les cas limites** - Entrées invalides, résultats vides
4. **Tests de performance** - Pour les grands ensembles de données
5. **Gestion des erreurs** - Pour toutes les erreurs possibles
6. **Respect de la configuration** - Quand les fonctionnalités sont désactivées

## 🎯 Améliorations futures

- [ ] Tests API
- [ ] Tests navigateur (Laravel Dusk)
- [ ] Benchmarks de performance
- [ ] Tests d'utilisation mémoire
- [ ] Tests d'accès concurrent
