<?php

namespace Rimelek\LDAPClient;

/**
 * "AND" filter
 */
class AndFilter extends FilterGroup
{

    /**
     * Sign of "AND"
     *
     * @var string $type
     */
    protected $type = '&';
}