<?php
namespace App\Services;

use App\Repositorys\FormRepository;

class FormService
{
    protected $formRepository;
    public function __construct()
    {
        $this->formRepository = new FormRepository();
    }

    public function getList($params)
    {
        $list = $this->formRepository->getList([], function ($query) use ($params) {
            $query->where('is_on', 1);
        });



    }
}
