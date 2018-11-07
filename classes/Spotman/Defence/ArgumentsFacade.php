<?php
declare(strict_types=1);

namespace Spotman\Defence;

class ArgumentsFacade
{
    /**
     * @param array                                         $requestArguments
     * @param \Spotman\Defence\ArgumentsDefinitionInterface $definition
     *
     * @return \Spotman\Defence\ArgumentsInterface
     */
    public function prepareArguments(
        array $requestArguments,
        ArgumentsDefinitionInterface $definition
    ): ArgumentsInterface {
        // Prepare named arguments based on a definition
        $namedArguments = $this->prepareNamedArguments($requestArguments, $definition);

        // Check for unnecessary arguments
        if (\count($requestArguments) > \count($namedArguments)) {
            throw new \InvalidArgumentException(
                \sprintf('Unnecessary arguments in a call, "%s" are allowed only',
                    implode('", "', \array_keys($namedArguments)))
            );
        }

        // Filter arguments` values
        $argumentsArray = $this->filter($namedArguments, $definition);

        // Return DTO
        return new Arguments($argumentsArray);
    }

    private function prepareNamedArguments(array $requestArguments, ArgumentsDefinitionInterface $definition): array
    {
        // Skip calls without arguments
        if (!$requestArguments) {
            return $requestArguments;
        }

        // Using named arguments already, skip processing
        if (\is_string(key($requestArguments))) {
            return $requestArguments;
        }

        $namedArguments = [];

        foreach ($definition->getRules() as $position => $rule) {
            $name = $rule->getName();

            if (array_key_exists($position, $requestArguments)) {
                $namedArguments[$name] = $requestArguments[$position];
            } elseif ($rule->isOptional()) {
                $namedArguments[$name] = $rule->getDefaultValue();
            } else {
                throw new \InvalidArgumentException('Missing argument ":name"', [
                    ':name' => $name,
                ]);
            }
        }

        return $namedArguments;
    }

    /**
     * @param array                                         $data
     * @param \Spotman\Defence\ArgumentsDefinitionInterface $definition
     *
     * @return array
     */
    private function filter(array $data, ArgumentsDefinitionInterface $definition): array
    {
        $filtered = [];

        foreach ($definition->getRules() as $rule) {
            $name = $rule->getName();

            if (!isset($data[$name]) && !$rule->isOptional()) {
                throw new \InvalidArgumentException('Key "'.$name.'" is required');
            }

            $targetKey = $rule->isIdentity()
                ? ArgumentsInterface::IDENTITY_KEY
                : $name;

            $filtered[$targetKey] = $this->filterRule($name, $data[$name], $rule);
        }

        return $filtered;
    }

    private function filterRule(string $name, $value, ArgumentRuleInterface $rule)
    {
        $defaultValue = $rule->getDefaultValue();

        switch (true) {
            case $rule->isIdentity():
                return $this->filterVar(
                    $name,
                    $value,
                    \FILTER_SANITIZE_SPECIAL_CHARS,
                    \FILTER_FLAG_STRIP_LOW + \FILTER_FLAG_STRIP_HIGH + \FILTER_FLAG_STRIP_BACKTICK,
                    $defaultValue
                );

            case $rule->isInt():
                return $this->filterVar(
                    $name,
                    $value,
                    \FILTER_VALIDATE_INT,
                    \FILTER_FLAG_ALLOW_OCTAL | \FILTER_FLAG_ALLOW_HEX,
                    $defaultValue
                );

            case $rule->isString():
                return $this->filterVar(
                    $name,
                    $value,
                    \FILTER_SANITIZE_STRING,
                    \FILTER_FLAG_STRIP_LOW + \FILTER_FLAG_STRIP_HIGH,
                    $defaultValue
                );

            case $rule->isEmail():
                return $this->filterVar(
                    $name,
                    $value,
                    \FILTER_VALIDATE_EMAIL,
                    \FILTER_FLAG_EMAIL_UNICODE,
                    $defaultValue
                );

            case $rule->isHtml():
                return $this->filterVar(
                    $name,
                    $value,
                    \FILTER_UNSAFE_RAW,
                    \FILTER_FLAG_NO_ENCODE_QUOTES,
                    $defaultValue
                );

            case $rule->isBool():
                return $this->filterVar(
                    $name,
                    $value,
                    \FILTER_VALIDATE_BOOLEAN,
                    \FILTER_NULL_ON_FAILURE,
                    $defaultValue
                );

            case $rule->isArray():
                if (!\is_array($value)) {
                    throw new \InvalidArgumentException('Array required for argument "'.$name.'"');
                }

                return $value;

            default:
                throw new \InvalidArgumentException('Unknown argument rule "'.$rule->getType().'"');
        }
    }

    private function filterVar(string $name, $value, int $type, $flags, $default)
    {
        $options = [];

        if ($default !== null) {
            $options['default'] = $default;
        }

        $value = \filter_var($value, $type, [
            'options' => $options,
            'flags'   => FILTER_NULL_ON_FAILURE | $flags,
        ]);

        if ($value === null) {
            throw new \InvalidArgumentException('Invalid argument value for "'.$name.'"');
        }

        return $value;
    }
}
