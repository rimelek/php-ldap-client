<?php

namespace Rimelek\LDAPClient;


class Filter
{

    const OP_EQUALITY = '=';

    const OP_GREATER_THAN = '>=';

    const OP_LESS_THAN = '<=';

    /**
     * contains
     */
    const OP_PROXIMITY = '~=';

    /**
     * Filter key
     *
     * Field name you want to search by
     *
     * @var string $key
     */
    private $key = 'objectClass';

    /**
     * @var string $value
     */
    private $value = '*';

    /**
     * Filter operator
     *
     * - "="
     * - "~="
     * - "<="
     * - ">="
     *
     * @var string $op
     */
    private $op = self::OP_EQUALITY;

    /**
     *
     * @var bool $negated
     */
    private $negated = false;

    /**
     *
     * filter as string
     *
     * @var string $string
     */
    private $string = null;

    /**
     * Set the filter as string
     *
     * @param string $string
     * @return Filter
     */
    public function setString($string)
    {
        $this->string = $string;
        return $this;
    }

    /**
     * Get filter as string
     *
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     *
     *
     * @param string $key Field name you want to search by
     * @param string $value Value/pattern you want to search
     * @param string $op Operator
     * @param bool $negation if true, the filter will be negated
     */
    public function __construct($key = null, $value = null, $op = null, $negation = false)
    {

        $v = ['key', 'value', 'op', 'negation'];

        foreach ($v as $_v) {
            if (is_null($$_v)) {
                continue;
            }
            $this->$_v = $$_v;
        }
    }

    /**
     * Set filter key
     *
     * (name of the field)
     *
     * @param string $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getKey() {
        return $this->key;
    }

    /**
     *
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
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
     * Set filter operator
     *
     * - "="
     * - "~="
     * - "<="
     * - ">="
     *
     * @param string $op
     * @return $this
     */
    public function setOp($op)
    {
        $this->op = $op;
        return $this;
    }

    /**
     * Filter operator
     *
     * - "="
     * - "~="
     * - "<="
     * - ">="
     */
    public function getOp()
    {
        return $this->op;
    }


    /**
     * Filter as string
     *
     * @return string
     */
    public function __toString()
    {
        if (!is_null($this->getString())) {
            return $this->getString();
        }

        $r = $this->key . $this->op . $this->value;

        if ($this->isNegated()) {
            $r = '!('.$r.')';
        }

        return '('.$r.')';
    }
}