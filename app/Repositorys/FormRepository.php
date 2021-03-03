<?php
namespace App\Repositorys;

use App\Model\FormModel;
use Taoran\Laravel\Repository;
use Taoran\Laravel\Exception\ApiException;

class FormRepository extends Repository
{
    protected $model;

    public function __construct()
    {
        $this->model = new FormModel();
    }
}
