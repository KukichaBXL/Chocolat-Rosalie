<?php
// path: model/manager/CategoryManager.php
// typage strict
declare(strict_types=1);

namespace model\manager;

use model\interface\ManagerInterface;
use model\MyPDO;

// les requêtes SQL sur la table `category`
class CategoryManager implements ManagerInterface
{
    private MyPDO $connect;

    public function __construct(MyPDO $connect)
    {
        $this->connect = $connect;
    }
}
