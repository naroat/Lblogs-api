<?php
namespace App\Repositorys;

use App\Model\ConfigModel;
use Taoran\Laravel\Repository;
use Taoran\Laravel\Exception\ApiException;

class ConfigRepository extends Repository
{
    protected $model;

    public function __construct()
    {
        $this->model = new ConfigModel();
    }
}
