<?php

namespace Dimer47\LaravelActivityTracker\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    /**
     * Fillable fields for a Profile.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'details',
        'userType',
        'userId',
        'route',
        'ipAddress',
        'userAgent',
        'locale',
        'referer',
        'methodType',
        'relId',
        'relModel',
    ];

    /**
     * The attributes that should be mutated.
     *
     * @var array
     */
    protected $casts = [
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
        'description'   => 'string',
        'details'       => 'string',
        'user'          => 'integer',
        'route'         => 'string',
        'ipAddress'     => 'string',
        'userAgent'     => 'string',
        'locale'        => 'string',
        'referer'       => 'string',
        'methodType'    => 'string',
        'relId'         => 'integer',
        'relModel'      => 'string',
    ];

    /**
     * Create a new instance to set the table and connection.
     *
     * @return void
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('LaravelActivityTracker.loggerDatabaseTable');
        $this->connection = config('LaravelActivityTracker.loggerDatabaseConnection');
    }

    /**
     * Get the database connection.
     */
    public function getConnectionName()
    {
        return $this->connection;
    }

    /**
     * Get the database connection.
     */
    public function getTableName()
    {
        return $this->table;
    }

    /**
     * An activity has a user.
     *
     * @var array
     */
    public function user()
    {
        return $this->hasOne(config('LaravelActivityTracker.defaultUserModel'));
    }

    /**
     * Get a validator for an incoming Request.
     *
     * @param array $merge (rules to optionally merge)
     *
     * @return array
     */
    public static function rules($merge = []): array
    {
        return array_merge(
            [
                'description'   => 'required|string',
                'details'       => 'nullable|string',
                'userType'      => 'required|string',
                'userId'        => 'nullable|integer',
                'route'         => 'nullable|url',
                'ipAddress'     => 'nullable|ip',
                'userAgent'     => 'nullable|string',
                'locale'        => 'nullable|string',
                'referer'       => 'nullable|string',
                'methodType'    => 'nullable|string',
                'relId'         => 'nullable|integer',
                'relModel'      => 'nullable|string',
            ],
            $merge
        );
    }

    /**
     * Get the related model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getRelatedModelAttribute()
    {
        if ($this->relModel && $this->relId && class_exists($this->relModel)) {
            return $this->relModel::find($this->relId);
        }

        return null;
    }

    /**
     * User Agent Parsing Helper.
     *
     * @return string
     */
    public function getUserAgentDetailsAttribute()
    {
        return \Dimer47\LaravelActivityTracker\App\Http\Traits\UserAgentDetails::details($this->userAgent);
    }
}
