<?php
declare(strict_types=1);

namespace Spotman\Api;

class ArgumentsDefinition implements ArgumentsDefinitionInterface
{
    /**
     * @var \Spotman\Api\ArgumentRuleInterface[]
     */
    private $rules;

    public function identity(string $name = null, bool $optional = null): ArgumentsDefinitionInterface
    {
        return $this->addRule($name ?? 'id', ArgumentRuleInterface::TYPE_IDENTITY, $optional);
    }

    public function int(string $name, bool $optional = null, $default = null): ArgumentsDefinitionInterface
    {
        return $this->addRule($name, ArgumentRuleInterface::TYPE_INTEGER, $optional, $default);
    }

    public function string(string $name, bool $optional = null, $default = null): ArgumentsDefinitionInterface
    {
        return $this->addRule($name, ArgumentRuleInterface::TYPE_STRING, $optional, $default);
    }

    public function email(string $name, bool $optional = null, $default = null): ArgumentsDefinitionInterface
    {
        return $this->addRule($name, ArgumentRuleInterface::TYPE_EMAIL, $optional, $default);
    }

    public function html(string $name, bool $optional = null, $default = null): ArgumentsDefinitionInterface
    {
        return $this->addRule($name, ArgumentRuleInterface::TYPE_HTML, $optional, $default);
    }

    public function bool(string $name, bool $optional = null, $default = null): ArgumentsDefinitionInterface
    {
        return $this->addRule($name, ArgumentRuleInterface::TYPE_BOOLEAN, $optional, $default);
    }

    public function array(string $name, bool $optional = null, $default = null): ArgumentsDefinitionInterface
    {
        return $this->addRule($name, ArgumentRuleInterface::TYPE_ARRAY, $optional, $default);
    }

    /**
     * Retrieve an external iterator
     *
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    private function addRule(string $name, string $type, ?bool $optional, $default = null): ArgumentsDefinitionInterface
    {
        foreach ($this->rules as $rule) {
            if ($rule->getName() === $name) {
                throw new \DomainException(\sprintf('Duplicate rule for argument "%s"', $name));
            }
        }

        $this->rules[] = new ArgumentRule($name, $type, $optional, $default);

        return $this;
    }
}
