<?php


namespace App\Repositorys;

use App\Model\ArticleModel;
use Taoran\Laravel\Repository;

class ArticleRepository extends Repository
{
    protected $article;

    public function __construct()
    {
        $this->article = new ArticleModel();
        parent::__construct($this->article);
    }
}