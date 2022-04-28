<?php

namespace App\GraphQL\Directives;

use Closure;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;

class JobDirective extends BaseDirective implements FieldMiddleware
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
"""
Dispatch a job after the resolution of a field.

The event constructor will be called with a single argument:
the resolved value of the field.
"""
directive @job(
  """
  Specify the fully qualified class name (FQCN) of the event to dispatch.
  """
  dispatch: String!
) repeatable on FIELD_DEFINITION
GRAPHQL;
    }

    public function handleField(FieldValue $fieldValue, Closure $next): FieldValue
    {
        $eventClassName = $this->namespaceClassName(
            $this->directiveArgValue('dispatch')
        );


        $fieldValue->resultHandler(function ($result) use ($eventClassName) {
            $eventClassName::dispatch($result);

            return $result;
        });

        return $next($fieldValue);
    }
}
