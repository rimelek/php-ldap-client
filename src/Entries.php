<?php

namespace Rimelek\LDAPClient;

class Entries extends Object
{
    /**
     * Search id
     *
     * Return value of ldap_search()
     *
     * @var resource
     */
    private $searchId = null;

    /**
     * LDAP result set
     *
     * @var Entry[]
     */
    private $entries = [];

    /**
     *
     *
     * @param LDAP $ldap LDAP connection object
     * @param resource $searchId search id
     */
    public function __construct(LDAP $ldap, $searchId) {
        $this->setLDAP($ldap);
        $this->searchId = $searchId;
        $this->entries = ldap_get_entries($ldap->getConn(), $searchId);
        unset($this->entries['count']);

        foreach ($this->entries as $i => &$entry) {
            $this->entries[$i] = new Entry($ldap, $entry);
        }
        parent::__construct($this->entries);
    }

    /**
     * Get search id
     *
     * return value of ldap_search()
     *
     * @return resource
     */
    public function getSearchId()
    {
        return $this->searchId;
    }
    /**
     * Number of found entries
     *
     * @return integer
     */
    public function getEntriesCount()
    {
        return count($this->entries);
    }

}
