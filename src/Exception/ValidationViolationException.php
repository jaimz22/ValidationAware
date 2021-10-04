<?php
declare(strict_types=1);
namespace VertigoLabs\ValidationAware\Exception;

use Exception;

class ValidationViolationException extends Exception
{
    public function __construct(string $taskName, int $violationCount, string $logGroup = null)
    {
        $msg = sprintf('%s failed validation with %d violations', $taskName, $violationCount);
        if (null !== $logGroup) {
            $msg .= sprintf(' | Log Group: %s',$logGroup);
        }
        parent::__construct($msg);
    }
}
