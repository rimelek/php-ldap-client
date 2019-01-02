<?php
namespace Rimelek\LDAPClient;

/**
 *
 */
class LDAPObject extends \ArrayIterator
{
    /**
     * LDAP connection object
     *
     * @var LDAP $ldap
     */
    private $ldap = null;

    /**
     * Get LDAP connection object
     *
     * @return LDAP
     */
    public function getLDAP()
    {
        return $this->ldap;
    }
    /**
     * Set LDAP connection object
     *
     * @param LDAP $ldap
     * @return $this
     */
    public function setLDAP(LDAP $ldap)
    {
        $this->ldap = $ldap;
        return $this;
    }
}