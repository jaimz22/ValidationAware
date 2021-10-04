<?php
declare(strict_types=1);
namespace VertigoLabs\ValidationAware;

use Symfony\Component\Validator\ConstraintViolationList;

interface ValidationAwareInterface
{
    public function isValid(): bool;
    public function validate(): ConstraintViolationList;
}
