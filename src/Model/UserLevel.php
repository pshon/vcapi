<?php

namespace VCAPI\Model;

use VCAPI\Common\Model;

    class UserLevel extends Model
{

    public $level;

    public $xp;

    public $nextLevelExperience;

    public function __construct($data = null)
    {
        $this->fillModel($data);
    }
}