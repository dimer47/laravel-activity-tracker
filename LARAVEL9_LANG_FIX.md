# Correction du répertoire de langues Laravel 9+

## Problème
Dans Laravel 9+, le répertoire de langues par défaut a été déplacé de `/resources/lang` vers `/lang`. Le package Laravel Activity Tracker utilisait encore l'ancienne structure, ce qui pouvait causer de la confusion et des conflits.

## Solution
Mise à jour du package pour supporter Laravel 9+ et les versions antérieures :

### Modifications effectuées :

1. **Déplacement des fichiers de langue** de `/src/resources/lang/` vers `/src/lang/`
2. **Mise à jour du ServiceProvider** pour détecter et utiliser la structure appropriée
3. **Ajout de la rétrocompatibilité** pour les anciennes versions de Laravel
4. **Mise à jour des commandes de publication** pour supporter les deux structures

### Mises à jour du ServiceProvider :

```php
// Charger les traductions depuis la nouvelle structure Laravel 9+ si disponible, sinon utiliser l'ancienne structure
if (is_dir(__DIR__.'/lang/')) {
    $this->loadTranslationsFrom(__DIR__.'/lang/', 'LaravelActivityTracker');
} else {
    $this->loadTranslationsFrom(__DIR__.'/resources/lang/', 'LaravelActivityTracker');
}
```

### Mises à jour de la publication :

```php
// Publier les fichiers de langue vers la structure Laravel 9+ si disponible, sinon utiliser l'ancienne structure
if (is_dir(__DIR__.'/lang/')) {
    // Structure Laravel 9+
    $this->publishes([
        __DIR__.'/lang' => base_path('lang/vendor/'.$publishTag),
    ], $publishTag);

    // Publier aussi vers l'ancienne structure pour la rétrocompatibilité
    $this->publishes([
        __DIR__.'/lang' => base_path('resources/lang/vendor/'.$publishTag),
    ], $publishTag.'-legacy');
} else {
    // Fallback vers l'ancienne structure
    $this->publishes([
        __DIR__.'/resources/lang' => base_path('resources/lang/vendor/'.$publishTag),
    ], $publishTag);
}
```

## Avantages :

- ✅ **Compatible Laravel 9+** - Utilise la nouvelle structure `/lang`
- ✅ **Rétrocompatible** - Fonctionne toujours avec les anciennes versions de Laravel
- ✅ **Pas de breaking changes** - Les installations existantes continuent de fonctionner
- ✅ **Prêt pour le futur** - Compatible Laravel 10+ et au-delà

## Utilisation :

### Projets Laravel 9+ :
```bash
php artisan vendor:publish --tag=LaravelActivityTracker
# Les fichiers de langue seront publiés dans /lang/vendor/LaravelActivityTracker/
```

### Projets Laravel legacy :
```bash
php artisan vendor:publish --tag=LaravelActivityTracker-legacy
# Les fichiers de langue seront publiés dans /resources/lang/vendor/LaravelActivityTracker/
```

## Références :
- [Changement du répertoire de langues Laravel 9](https://laravel.com/docs/9.x/upgrade#language-directory)
