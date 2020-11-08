<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdminUserModel extends Model
{
    protected $table = 'admin_user';

    protected $dateFormat = 'U';

    public static function getAdminUser()
    {

    }

}
