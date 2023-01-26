<?php

namespace App\Validator\Constraints;
use Symfony\Component\Translation\Extractor\Visitor\ConstraintVisitor;
use Symfony\Component\Validator\Constraint;

class ValidateDate extends Constraint 
{
    public $CLOSE = "Restaurant fermé.";
    public $FULL = "Restaurant complet.";
}