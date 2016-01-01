<?php

namespace VCAPI\Model;

use VCAPI\Common\Model;

class MilitaryRank extends Model
{
    public $name;

    public $image;

    public function __construct($data = null)
    {
        $this->fillModel($data);
    }
}