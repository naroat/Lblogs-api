<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ArticleModel extends Model
{
    protected $table = 'article';

    protected $dateFormat = 'U';
}
