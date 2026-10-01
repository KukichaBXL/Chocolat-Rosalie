<?php
// path: model/abstract/AbstractMapping.php
// typage strict
// declare(strict_types=1);

namespace model\abstract;

// classe qui ne peut pas être instanciée
abstract class AbstractMapping
{

    // va être hérité par les enfants
    public function __construct(array $datas){
        // $this représente un enfant
        $this->hydrate($datas);
    }

    // création d'une méthode d'hydratation
    // génération du nom des setters via les clef du tableau
    protected function hydrate(array $datas):void
    {
        // tant qu'on a des données dans le tableau
        foreach($datas as $setter=>$value){
            // création du nom du setter
            $setterName = "set".str_replace("_","",ucwords($setter, '_'));
            //echo "$setterName<br>";
            // vérification de l'existance du setter dans la classe enfant
            if(method_exists($this,$setterName))
                // appel du setter existant avec la valeur passée en paramètre
                $this->$setterName($value);
            
        }
    }


    // méthode qui DOIT être implémenté dans ses enfants;
    // peut être remplacée par les interfaces
    // abstract public function maMethode(): string;

}