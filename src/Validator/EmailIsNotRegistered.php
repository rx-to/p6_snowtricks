<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EmailIsNotRegistered extends Constraint
{
    public $message = "Cette adresse email est déjà utilisée.";
    public $mode = 'strict'; // If the constraint has configuration options, define them as public properties
}
