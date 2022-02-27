<?php

namespace App\GraphQL\Schema\Types;

use Exception;
use GraphQL\Type\Definition\EnumType;
use InvalidArgumentException;

/**
 * A convenience wrapper for registering enums programmatically.
 */
class NativeEnumType extends EnumType
{
    protected string $enumClass;

    /**
     * Create a GraphQL enum from a native enum type.
     *
     * @param  string  $enumClass
     * @param  string|null  $name  The name the enum will have in the schema, defaults to the basename of the given class
     */
    public function __construct(string $enumClass, ?string $name = null)
    {
        if (! enum_exists($enumClass)) {
            throw new InvalidArgumentException(
                "Must pass an Enum instance, got {$enumClass}."
            );
        }

        $this->enumClass = $enumClass;
        parent::__construct([
            'name' => $name ?? class_basename($enumClass),
            'values' => array_reduce($enumClass::cases(),
                function (array $acc, \BackedEnum $enum) {
                    $acc[$enum->value] = [
                        'value' => $enum->value,
                        'description' => '' // todo: Add description for enums
                    ];
                    return $acc;
                },
                []
            ),
        ]);
    }

    /**
     * Overwrite the native EnumType serialization, as this class does not hold plain values.
     * @throws Exception
     */
    public function serialize($value): string
    {
        if (! $value instanceof \BackedEnum) {
            throw new Exception();
        }

        return $value->value;
    }
}
