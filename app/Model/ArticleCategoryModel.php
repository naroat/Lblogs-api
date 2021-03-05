<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ArticleCategoryModel extends Model
{
    protected $table = 'article_category';

    protected $dateFormat = 'U';

    protected $hidden = ['is_on'];
}
