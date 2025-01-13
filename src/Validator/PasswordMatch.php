<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PasswordMatch extends Constraint
{
    public $message = 'The password fields must match.';
}