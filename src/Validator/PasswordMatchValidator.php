<?php

namespace App\Validator;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordMatchValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof FormInterface) {
            throw new \LogicException('The PasswordMatch constraint can only be applied to forms.');
        }

        $plainPassword = $value->get('plainPassword')->getData();
        $confirmPassword = $value->get('confirm_password')->getData();

        if ($plainPassword !== $confirmPassword) {
            $this->context->buildViolation($constraint->message)
                ->atPath('confirm_password')
                ->addViolation();
        }
    }
}