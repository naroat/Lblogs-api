<?php


namespace App\Repositorys;

use App\Model\ArticleModel;
use Taoran\Laravel\Exception\ApiException;
use Taoran\Laravel\Repository;

class ArticleRepository extends Repository
{
    protected $article;

    public function __construct()
    {
        $this->article = new ArticleModel();
        parent::__construct($this->article);
    }

    public function delete($id)
    {
        $info = $this->getOne($id);
        if (!$info) {
            throw new ApiException();
        }

        $res = $this->update($info, [
            'is_on' => 0
        ]);
        if (!$res) {
            throw new ApiException();
        }

        return true;
    }
}