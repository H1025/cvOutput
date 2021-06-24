<?php

namespace cvOutput\language;

use mihoshi\hashValidator\rule\hashRule;

class ClassRule extends hashRule
{

    /** @var bool $abstract */
    private $abstract;
    private $extend;
    private $nameSpace;

    /**
     * ClassRule constructor.
     * @param array $rule
     */
    public function __construct($rule)
    {
        parent::__construct($rule);
        $this->abstract = $rule['abstract'] ?? false;
        $this->extend = $rule['extend'] ?? '';
    }

    public function check($value)
    {
        return parent::check($value);
    }

    public function dump(): array
    {
        $return = array_merge(parent::dump(), [
            'abstract' => $this->abstract,
            'extend' => $this->extend,
            'type' => 'class',
            'nameSpace' => $this->nameSpace,
        ]);
        return $return;
    }
}
