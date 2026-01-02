<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Dimer47\LaravelActivityTracker\App\Models\Activity;

return new class() extends Migration {
    /**
     * Track if this migration actually added columns.
     */
    private static bool $addedRelId = false;
    private static bool $addedRelModel = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $activity = new Activity();
        $connection = $activity->getConnectionName();
        $tableName = $activity->getTableName();
        $schema = Schema::connection($connection ?? config('database.default'));

        if (!$schema->hasTable($tableName)) {
            return;
        }

        $schema->table($tableName, function (Blueprint $table) use ($schema, $tableName) {
            if (!$schema->hasColumn($tableName, 'relId')) {
                $table->unsignedBigInteger('relId')->index()->nullable();
                self::$addedRelId = true;
            }

            if (!$schema->hasColumn($tableName, 'relModel')) {
                $table->string('relModel')->nullable();
                self::$addedRelModel = true;
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * Note: This migration only drops columns if IT added them during up().
     * If the columns were created by the initial table creation migration,
     * that migration's down() will handle cleanup by dropping the entire table.
     */
    public function down(): void
    {
        // Only drop columns if this migration added them
        if (!self::$addedRelId && !self::$addedRelModel) {
            return;
        }

        $activity = new Activity();
        $connection = $activity->getConnectionName();
        $tableName = $activity->getTableName();
        $schema = Schema::connection($connection ?? config('database.default'));

        if (!$schema->hasTable($tableName)) {
            return;
        }

        $schema->table($tableName, function (Blueprint $table) use ($schema, $tableName) {
            if (self::$addedRelId && $schema->hasColumn($tableName, 'relId')) {
                $table->dropIndex(['relId']);
                $table->dropColumn('relId');
            }

            if (self::$addedRelModel && $schema->hasColumn($tableName, 'relModel')) {
                $table->dropColumn('relModel');
            }
        });
    }
};
