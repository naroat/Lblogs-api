<?php


namespace App\Services;


use App\Repositorys\ArticleRepository;

class ArticleService
{
    protected $articleRepository;

    public function __construct()
    {
        $this->articleRepository = new ArticleRepository();
    }

    public function getList()
    {
        return $this->articleRepository->getList();
    }

    public function getOne($id)
    {
        return $this->articleRepository->getOneById($id);
    }

    public function add($param)
    {
        return $this->articleRepository->create($param);
    }

    public function update($param, $id)
    {
        $data = $this->articleRepository->getOne($id);

        return $this->articleRepository->update($data, $param);
    }

    public function delete($id)
    {
        return $this->articleRepository->delete($id);
    }
}