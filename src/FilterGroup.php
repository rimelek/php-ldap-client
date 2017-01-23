<?php

namespace Rimelek\LDAPClient;

abstract class FilterGroup extends FilterOrConnection
{
    /**
     *
     * @var Filter|FilterGroup $filters
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
            if (is_object($filter)) {
                if ($filter instanceof Filter) {
                    $this->addFilter($filter);
                } else if ($filter instanceof FilterGroup) {
                    $this->addFilterConnection($filter);
                }
            }
        }
    }

    /**
     * Add a new filter
     *
     * @param Filter $filter
     * @return $this
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * Add a new filter connection
     *
     * @param FilterGroup $conn
     * @return $this
     */
    public function addFilterConnection(FilterGroup $conn)
    {
        $this->filters[] = $conn;
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