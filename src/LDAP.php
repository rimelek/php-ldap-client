<?php

namespace Rimelek\LDAPClient;

use Rimelek\LDAPClient\Exception\BindException;
use Rimelek\LDAPClient\Exception\EntryExistsException;
use Rimelek\LDAPClient\Exception\InvalidSyntaxException;
use Rimelek\LDAPCLient\Exception\ObjectClassViolationException;
use Rimelek\LDAPClient\Exception\UndefinedAttributeTypeException;

/**
 * LDAP connection
 * @package Rimelek\LDAPClient
 */
class LDAP
{
    const SCOPE_SUB = 'sub';
    const SCOPE_ONE = 'one';
    const SCOPE_BASE = 'base';

    const DEFAULT_OPT_PROTOCOL_VERSION = 3;
    const DEFAULT_OPT_REFERRALS = false;

    /**
     * Server address
     *
     * Ex.: ldap.domain.tld
     *
     * @var string $server
     */
    private $server = null;

    /**
     *
     * @var string $password
     */
    private $password = null;

    /**
     * Base DN
     *
     * @var string $baseDn
     */
    private $baseDn = '';

    /**
     *
     * @var string $managerDn
     */
    private $managerDn = '';

    /**
     * one / sub / base
     * @var string $scope
     */
    private $scope = self::SCOPE_SUB;

    /**
     * Port
     *
     * @var int $port
     */
    private $port = 389;

    /**
     * Resource id
     *
     * @var resource $conn
     */
    private $conn = null;

    /**
     * Data of current session
     *
     * @var array $options
     */
    private $options = array();

    /**
     * Limit of result set
     * @var int $limit
     */
    private $limit = 0;

    /**
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options + self::getDefaultOptions());
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return LDAP
     */
    public function setLimit(int $limit): LDAP
    {
        $this->limit = $limit;
        return $this;
    }


    /**
     *
     * @see ldap_set_option()
     *
     * @param $key
     * @param $value
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    /**
     *
     * @see ldap_get_option()
     *
     * @param int $key
     * @return mixed
     */
    public function getOption($key)
    {
        return $this->options[$key] ?? null;
    }

    /**
     *
     * @see ldap_set_option()
     *
     * @param array $options multiple key-value pair for ldap_set_option
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     *
     * @see ldap_get_option()
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }


    public static function getDefaultOptions()
    {
        return [
            LDAP_OPT_PROTOCOL_VERSION => self::DEFAULT_OPT_PROTOCOL_VERSION,
            LDAP_OPT_REFERRALS => self::DEFAULT_OPT_REFERRALS,
        ];
    }

    /**
     *
     *
     * @return $this
     */
    public function unbind() {
        if (is_resource($this->getConn())) {
            ldap_unbind($this->getConn());
        }
        return $this;
    }

    /**
     * Close the connection
     *
     */
    public function close()
    {
        if (is_resource($this->getConn())) {
            ldap_close($this->getConn());
        }
    }


    /**
     * Search
     *
     * @param Filter|FilterGroup $filter Filters
     * @param Attr[] $attributes Attributes
     * @return Search
     */
    public function search($filter, $attributes = [])
    {
        return new Search($this, $filter, $attributes);
    }

    /**
     *
     * @see \LDAP_OPT_REFERRALS
     *
     * @param bool $referrals
     * @return $this
     */
    public function setReferrals($referrals = true)
    {
        $this->setOption(LDAP_OPT_REFERRALS, (bool) $referrals);
        return $this;
    }

    /**
     *
     *
     * @return bool
     */
    public function isReferrals()
    {
        return $this->getOption(LDAP_OPT_REFERRALS);
    }

    /**
     *
     * @param int $port
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = (int) $port;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getPort()
    {
        return (int) $this->port;
    }

    /**
     * Set the connection's resource id
     *
     * @param resource $conn Azonosító
     * @return $this
     */
    private function setConn($conn) {
        $this->conn = $conn;
        return $this;
    }

    /**
     * Get the connection's resource id
     *
     * @return resource
     */
    public function getConn()
    {
        return $this->conn;
    }

    /**
     * Connect to the LDAP database
     *
     * @return $this
     */
    public function connect()
    {
        $this->setConn(ldap_connect($this->getServer(), $this->getPort()));

        foreach ($this->getOptions() as $key => $value) {
            ldap_set_option($this->getConn(), $key, $value);
        }
        return $this;
    }

    /**
     *
     * @param string $rdn
     * @param string $password
     * @return $this
     * @throws BindException
     */
    public function bind($rdn = null, $password = null)
    {
        $argc = func_num_args();

        if(!@ldap_bind($this->getConn(),
                !$argc ? $this->getManagerDn() : $rdn,
                $argc < 2 ? $this->getPassword() : $password) ) {
            $error = error_get_last();
            throw new BindException($error['message'], 0, $error['type'], $error['file'], $error['line']);
        }
        return $this;
    }

    /**
     *
     * @param int $protocolVersion
     * @return $this
     */
    public function setProtocolVersion($protocolVersion)
    {
        $this->setOption(LDAP_OPT_PROTOCOL_VERSION, (int) $protocolVersion);
        return $this;
    }
    /**
     *
     * @return int
     */
    public function getProtocolVersion()
    {
        return $this->getOption(LDAP_OPT_PROTOCOL_VERSION);
    }
    /**
     *
     * @param string $managerDn
     * @return $this
     */
    public function setManagerDn($managerDn)
    {
        $this->managerDn = $managerDn;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getManagerDn()
    {
        return $this->managerDn;
    }

    /**
     *
     * @param string $scope
     * @return $this
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     *
     * @param string $baseDn
     * @return $this
     */
    public function setBaseDn($baseDn)
    {
        $this->baseDn = $baseDn;
        return $this;
    }
    /**
     *
     * @return string
     */
    public function getBaseDn()
    {
        return $this->baseDn;
    }

    /**
     *
     * @param string $server
     * @return $this
     */
    public function setServer($server)
    {
        $this->server = $server;
        return $this;
    }

    /**
     *
     */
    public function getServer()
    {
        return $this->server;
    }
    /**
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }
    /**
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Escape special characters: (, ), &, |, =, >, <, ~, *, /, \
     *
     * @param string $string
     * @return string Escaped string
     */
    public static function escape($string)
    {
        $chars = [
            '(' => '\28', ')' => '\29', '&' => '\26',
            '|' => '\7c', '=' => '\3d', '>' => '\3e',
            '<' => '\3c', '~' => '\7e', '*' => '\2a',
            '/' => '\2f', '\\' => '\5c'
        ];

        return str_replace(array_keys($chars), array_values($chars), $string);
    }

    /**
     * Add a new entry
     *
     * @param Entry $entry
     */
    public function addEntry(Entry $entry)
    {
        if (!is_resource($this->getConn())) {
            $this->connect()->bind();
        }

        $entryArray= array();
        foreach ($entry as $attr) {
            /* @var $attr Attr */
            foreach ($attr->getValues() as $value) {
                $entryArray[$attr->getName()][] = $value;
            }
        }

        $entry->setDN($entry->getDN());
        if(!@ldap_add($this->getConn(), $entry->getDN(), $entryArray)) {
            // TODO: remove var_dump
            var_dump($entryArray);
            self::throwExceptionWithLastError();
        }
    }


    /**
     * @param array $error
     * @throws EntryExistsException
     * @throws InvalidSyntaxException
     * @throws ObjectClassViolationException
     * @throws UndefinedAttributeTypeException
     */
    public static function throwExceptionWithError(array $error)
    {
        if(!preg_match('~ldap_add\(\)[^:]*\:\s+(?P<type>.*)\:\s+(?P<msg>.*)$~i', strtolower($error['message']), $m)) {
            return;
        }
        // TODO: Remove var_dump
        var_dump($error);
        switch ($m['type']) {
            case 'add':

                switch ($m['msg']) {
                    case 'already exists':
                        throw new EntryExistsException($error['message'], 0, 1, $error['file'], $error['line']);
                        break;
                    case 'undefined attribute type':
                        throw new UndefinedAttributeTypeException($error['message'], 0, 1, $error['file'], $error['line']);
                        break;
                    case 'invalid syntax':
                        throw new InvalidSyntaxException($error['message'], 0, 1, $error['file'], $error['line']);
                        break;
                    case 'object class violation':
                        throw new ObjectClassViolationException($error['message'], 0, 1, $error['file'], $error['line']);
                        break;
                }


                break;
        }
    }

    /**
     *
     */
    public static function throwExceptionWithLastError()
    {
        self::throwExceptionWithError(error_get_last());
    }

}
