<?php

namespace Rimelek\LDAPClient;


class Search extends LDAPObject
{

    /**
     * @var LDAP $ldap
     */
    private $ldap;

    /**
     * Search id
     *
     * return value of ldap_search
     *
     * @var resource $searchId
     */
    private $searchId = null;

    /**
     *
     *
     * @param LDAP $ldap LDAP connection object
     * @param Filter|FilterGroup $filter
     * @param string[] $attributes key => value
     * @throws Exception\BindException
     */
    public function __construct(LDAP $ldap, $filter, $attributes)
    {
        if (!is_resource($ldap->getConn())) {
            $ldap->connect()->bind();
        }
        $this->ldap = $ldap;

        $deref = $ldap->getOption(LDAP_OPT_DEREF);
        $timeout = (int) $ldap->getOption(LDAP_OPT_TIMELIMIT);
        $limit = $ldap->getLimit();

		switch($ldap->getScope()) {
			case 'one':
				$this->searchId = ldap_list($ldap->getConn(), $ldap->getBaseDn(), $filter, $attributes, 0, $limit, $timeout, $deref);
				break;
			case 'sub':
				$this->searchId = ldap_search($ldap->getConn(), $ldap->getBaseDn(), $filter, $attributes, 0, $limit, $timeout, $deref);
				break;
			case 'base':
				$this->searchId = ldap_read($ldap->getConn(), $ldap->getBaseDn(), $filter, $attributes, 0, $limit, $timeout, $deref);
				break;
		}

		parent::__construct($attributes);
    }

    /**
     * Get resource id of search
     *
     * @return resource
     */
    public function getSearchId()
    {
        return $this->searchId;
    }

	/**
     * Search results
	 *
	 * @return Entries|Entry[]
	 */
	public function getEntries()
    {
		return new Entries($this->ldap, $this->getSearchId());
	}

	/**
     * Return value of ldap_get_entries
     *
     * @see ldap_get_entries()
     *
	 * @return array
	 */
	public function getResult()
    {
		$ldap = $this->getLDAP();

		return ldap_get_entries($ldap->getConn(), $this->getSearchId());
	}


}