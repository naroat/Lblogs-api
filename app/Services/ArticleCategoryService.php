<?php


namespace App\Services;


use App\Repositorys\ArticleCategoryRepository;

class ArticleCategoryService
{
    protected $articleCategoryRepository;

    public function __construct()
    {
        $this->articleCategoryRepository = new ArticleCategoryRepository();
    }

    public function getList()
    {
        return $this->articleCategoryRepository->getList();
    }

    public function getOne($id)
    {
        return $this->articleCategoryRepository->getOneById($id);
    }

    public function add($param)
    {
        return $this->articleCategoryRepository->add($param);
    }

    public function update($param, $id)
    {
        $data = $this->articleCategoryRepository->getOneById($id);

        return $this->articleCategoryRepository->update($data, $param);
    }

    public function delete($id)
    {
        return $this->articleCategoryRepository->delete($id);
    }
}