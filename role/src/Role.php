<?php
namespace Ohio\Core\Role;

use Ohio\Core\Base\BaseModel;

class Role extends BaseModel
{

    protected $morphClass = 'Role';

    protected $table = 'roles';

    protected $guarded = ['id'];

    public function __toString()
    {
        return (string) $this->name;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(trim($value));
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = str_slug(trim($value));
    }

}