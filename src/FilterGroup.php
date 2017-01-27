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
     * @var bool $negated
     */
    private $negated = false;

    /**
     *
     * @todo Implement negation
     *
     * @param Filter[]|FilterGroup[] $filters
     * @param bool $negated
     */
    public function __construct(array $filters, $negated = false)
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }
        $this->negated = $negated;
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
     * Negate the filter
     *
     *
     * @param bool $negated
     * @return $this
     */
    public function setNegated($negated = true)
    {
        $this->negated = (bool)$negated;
        return $this;
    }

    /**
     * Tagadva van-e a feltétel
     *
     * @return bool
     */
    public function isNegated()
    {
        return (bool)$this->negated;
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

        if ($this->isNegated()) {
            $s = '(!' . $s . ')';
        }

        return $s;
    }
}