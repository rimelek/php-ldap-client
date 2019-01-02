<?php

namespace Rimelek\LDAPClient;

/**
 * LDAP attribute
 */
class Attr extends \ArrayIterator
{
    /**
     * @var array $values
     */
    private $values = [];

    /**
     * @var string $name
     */
    private $name = '';

    /**
     *
     *
     * @param string $name
     * @param array $values
     */
    public function __construct($name, $values)
    {
        $this->setValues($values);
        $this->setName($name);
        parent::__construct($this->getValues());
    }

    /**
     * Attribútum értékeinek lekérdezése
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     *
     * @param string|array $values
     * @return $this
     */
    public function setValues($values)
    {
        if (!$values) {
            $values = array();
        }
        if (!is_array($values)) {
            $values = array($values);
        }
        unset($values['count']); // TODO: ?
        $this->values = $values;

        return $this;
    }
    /**
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string the values separated by commas
     */
    public function __toString()
    {
        return implode(', ',$this->getValues());
    }

}
