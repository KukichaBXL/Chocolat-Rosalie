<?php
// path: model/interface/ManagerInterface.php
// typage strict
declare(strict_types=1);

namespace model\interface;

use model\MyPDO;

// tous les managers reçoivent la connexion à la base
interface ManagerInterface
{
    public function __construct(MyPDO $connect);
}
