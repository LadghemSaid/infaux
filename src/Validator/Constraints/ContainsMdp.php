<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
* @Annotation
*/
class ContainsMdp extends Constraint
{
public $message = 'Le mot de passe "{{ string }}" contient un character illegale.';
}
