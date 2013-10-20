<?php

namespace Matthias\SymfonyDependencyInjectionTest\PhpUnit;

use Symfony\Component\DependencyInjection\Definition;

class DefinitionHasMethodCallConstraint extends \PHPUnit_Framework_Constraint
{
    private $methodName;
    private $arguments;

    public function __construct($methodName, array $arguments)
    {
        $this->methodName = $methodName;
        $this->arguments = $arguments;
    }

    public function evaluate($other, $description = '', $returnResult = false)
    {
        if (!($other instanceof Definition)) {
            throw new \InvalidArgumentException(
                'Expected an instance of Symfony\Component\DependencyInjection\Definition'
            );
        }

        foreach ($other->getMethodCalls() as $methodCall) {
            list($method, $arguments) = $methodCall;

            if ($method !== $this->methodName) {
                continue;
            }

            if ($this->equalArguments($this->arguments, $arguments)) {
                return true;
            }
        }

        if (!$returnResult) {
            $this->fail(
                $other,
                sprintf(
                    'None of the method calls matched the expected method "%s" with arguments %s',
                    $this->methodName,
                    \PHPUnit_Util_Type::export($this->arguments)
                )
            );
        }

        return false;
    }

    public function toString()
    {
        return sprintf(
            'has a method call to "%s" with the given arguments',
            $this->methodName
        );
    }

    private function equalArguments($expectedArguments, $actualArguments)
    {
        $constraint = new \PHPUnit_Framework_Constraint_IsEqual(
            $expectedArguments
        );

        return $constraint->evaluate($actualArguments, '', true);
    }
}
