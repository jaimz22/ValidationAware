<?php
declare(strict_types=1);
namespace VertigoLabs\ValidationAware;

use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use VertigoLabs\DataAware\DataAware;
use VertigoLabs\LoggerAware\LoggerAware;

trait ValidationAware
{
    use LoggerAware;
    use DataAware;

    /**
     * @var bool The current validity status
     */
    protected bool $valid = false;

    /**
     * Returns a collection of validation constraints
     *
     * @link https://symfony.com/doc/3.4/validation.html#constraints
     *
     * @return null|Collection A collection of constraints
     */
    protected function defineValidationConstraints(): ?Collection
    {
        return null;
    }

    /**
     * Returns the current validity of the data
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * Validates input data.
     *
     * @return ConstraintViolationList
     * @throws \VertigoLabs\DataAware\Exceptions\DataNotFoundNoDefaultException
     */
    final public function validate(): ConstraintViolationList
    {
        $this->log('Validation | Beginning');
        $this->valid = false;
        $validator = Validation::createValidator();

        $constraints = $this->defineValidationConstraints();

        if ($constraints === null) {
            $this->valid = true;
            $this->log('Validation | Skipped');

            return new ConstraintViolationList();
        }

        if (!($constraints instanceof Constraint)) {
            throw new RuntimeException(__CLASS__.'::defineValidationConstraints() must return an instance of '.Constraint::class.' or null');
        }

        /** @var ConstraintViolationList $violations */
        $violations = $validator->validate($this->getData(), $constraints);

        $this->valid = ($violations->count() === 0);

        if (!$this->valid) {
            $this->log('Validation | Violations found.');
            foreach ($violations as $violation) {
                $this->log(sprintf('Validation | %s: %s', $violation->getPropertyPath(), $violation->getMessage()));
            }
        }

        $this->log('Validation | Completed');
        return $violations;
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    final public static function getValidationConstraintFields(): array
    {
        $refl = new ReflectionClass(static::class);
        $refl->getMethod('defineValidationConstraints')->setAccessible(true);
        /** @var Collection $constraints */
        $constraints = ($refl->newInstanceWithoutConstructor())->defineValidationConstraints();
        return $constraints->fields;
    }
}
