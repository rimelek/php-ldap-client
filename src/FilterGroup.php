<?php

namespace Rimelek\LDAPClient;

use TypeError;

abstract class FilterGroup
{
    /**
     *
     * @var Filter[]|FilterGroup[] $filters
     */
    protected $filters = array();

    /**
     * Filter type
     * @var string $type
     */
     protected $type;

    /**
     *
     * @todo Implement negation
     *
     * @param Filter[]|FilterGroup[] $filters
     */
    public function __construct(array $filters)
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }
    }

    /**
     * Add a new filter
     *
     * @param Filter|FilterGroup $filter
     * @return $this
     * @throws TypeError
     */
    public function addFilter($filter)
    {
        if ( !$filter instanceof Filter and !$filter instanceof FilterGroup) {
            throw new TypeError('First argument of '
                . __METHOD__ . ' must be an instance of '
                . Filter::class . ' or ' . FilterGroup::class . '. ' . (
                    is_object($filter) ? get_class($filter) : gettype($filter)  . ' was given.'
                ));
        }
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * Type of filter
     *
     * - "&" (and)
     * - "|" (or)
     *
     * @return string
     */
    protected function getType()
    {
        return $this->type;
    }

    /**
     * Filters rendered as string
     *
     * This is the exact LDAP filter that the server expect to search
     *
     * @return string
     */
    public function __toString()
    {
        $s = '(' . $this->getType();
        foreach ($this->filters as $filter) {
            $s .= $filter;
        }
        $s .= ')';
        return $s;
    }
}