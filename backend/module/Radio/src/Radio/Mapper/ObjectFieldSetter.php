<?php
namespace Radio\Mapper;

class ObjectFieldSetter implements FieldSetter
{

    private $em;

    function __construct($em = null)
    {
        $this->em = $em;
    }


    function set(&$container, $propertyName, &$value)
    {
        $setter = "set" . strtoupper(substr($propertyName, 0, 1)) . substr($propertyName, 1);
        $container->$setter($value);
    }

    function get(&$container, $propertyName)
    {
        $getter = "get" . strtoupper(substr($propertyName, 0, 1)) . substr($propertyName, 1);
        return $container->$getter();
    }

    function ensureExists(&$container, $propertyName, $type, $originalChild)
    {
        $getter = "get" . $propertyName;

        if (!empty($originalChild)) {
            $t = new $type();
            $idGetter = "getId";
            if ($this->em != null && method_exists($t, "getId") && !empty($originalChild)
                && array_key_exists("id", $originalChild)
            ) {
                $r = $this->em->find($type, $originalChild["id"]);
                if (!empty($r)) {
                    $t = $r;
                }
            }

            $this->set($container, $propertyName, $t);
        }
        return $container->$getter();

    }

    public function add(&$container, $propertyName, &$value)
    {
        $setter = "add" . $propertyName;
        $container->$setter($value);
    }

    public function findChild($from, $type)
    {
        if ($this->em != null && $type !=null && array_key_exists("id",$from)) {
            $original = $this->em->find($type, $from['id']);
            if ($original) {
                return $original;
            }
        }
        return new $type();
    }
}

?>
