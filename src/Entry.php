<?php
namespace Rimelek\LDAPClient;


class Entry extends Object
{

    /**
     * Entry DN
     *
     * @var string
     */
    private $dn = '';

    /**
     *
     * @var Attr[]
     */
    private $attributes = [];

    /**
     *
     * @param string $dn
     * @return $this
     */
    public function setDN($dn)
    {
        $this->dn = $dn;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getDN()
    {
        return $this->dn;
    }

    /**
     *
     * @param Attr[]|array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = array();
        foreach ($attributes as $name => &$attr) {
            $this->attributes[$name] = $attr instanceof Attr ? $attr : new Attr($name, $attr);
        }

        return $this;
    }

    /**
     *
     * @return Attr[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get attribute by name
     *
     * @param string $name
     * @return Attr
     */
    public function getAttribute($name)
    {
        $name = strtolower($name);
        return $this->attributes[$name] ?? null;
    }

    /**
     *
     * @param LDAP $ldap LDAP connection object
     * @param array $entry The original array of an entry containing
     * search path and the number of attributes
     */
    public function __construct(LDAP $ldap, $entry)
    {
        $this->setLDAP($ldap);

        if (isset($entry['count'])) {
            for($i = 0; $i < $entry['count']; $i++) {
                unset($entry[$i]);
            }
        }
        if (isset($entry['dn'])) {
            $this->setDN($entry['dn']);
            unset($entry['dn'], $entry['count']);
        }

        $this->setAttributes($entry);
        parent::__construct($this->getAttributes());
    }

    /**
     * One entry as a string
     *
     * It is for debug purposes
     *
     * @return string
     */
    public function __toString()
    {
        $s = '';
        foreach ($this as $key => $attr) {
            $s .=  $key . ': ' . $attr . PHP_EOL;
        }
        return $s;
    }

}