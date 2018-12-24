<?php
namespace entities;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\PersistentCollection;

class ProtoForGraph {
    protected $renamedKeys = [];

    public function mapEntityCollectionTpGraph(PersistentCollection $entCollection) {
        return $entCollection->map(
            function($subEnt){
                return $subEnt->getGraphArray();
            }
         )->toArray();
    }

    public function getGraphArray() {
        if (method_exists($this, 'initialRenamedArray')) $this->initialRenamedArray();

        $res = [];
        foreach ($this as $varName => $value) {
            $subKey = explode(':', $varName)[0];

            if (array_key_exists($subKey, $this->renamedKeys)) {

                $rewriteArr = is_object($this->renamedKeys[$subKey])
                    ? call_user_func($this->renamedKeys[$subKey])
                    : array( $subKey => $this->renamedKeys[$subKey] );

                foreach ($rewriteArr as $rewriteKey => $rewriteValue) {
                    $res[$rewriteKey] = $rewriteValue;
                }

            } else if ($subKey !== 'renamedKeys' && !preg_match("/^\__\w+\__$/", $subKey)) {

                if ($value instanceof PersistentCollection) {

                    $this->mapEntityCollectionTpGraph($value);

                } else if ($value instanceof Entity) {

                    $res[$subKey] = $value->getGraphArray();

                } else {

                    $res[$subKey] = $value;
                }
            }
        }
        return $res;
    }
}