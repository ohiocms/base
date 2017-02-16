<?php
namespace Belt\Core;

use Belt;
use Illuminate\Database\Eloquent\Model;

class Param extends Model
{
    /**
     * @var string
     */
    protected $morphClass = 'params';

    /**
     * @var string
     */
    protected $table = 'params';

    /**
     * @var array
     */
    protected $fillable = ['key', 'value'];

    public function setKeyAttribute($value)
    {
        $this->attributes['key'] = strtolower(trim($value));
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = trim($value);
    }

}