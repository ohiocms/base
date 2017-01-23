<?php
namespace Ohio\Core\Team;

use Ohio\Core\User\User;
use Ohio\Core\Base\Behaviors\SluggableTrait;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{

    use SluggableTrait;

    protected $morphClass = 'teams';

    protected $table = 'teams';

    protected $fillable = ['name'];

    /**
     * Default values
     *
     * @var array
     */
    protected $attributes = [
        'is_active' => 1,
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'team_users', 'team_id', 'user_id');
    }

    public function setIsActiveAttribute($value)
    {
        $this->attributes['is_active'] = boolval($value);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(trim($value));
    }

}