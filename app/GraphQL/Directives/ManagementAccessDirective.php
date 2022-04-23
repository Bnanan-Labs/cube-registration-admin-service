<?php

namespace App\GraphQL\Directives;

use App\Models\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Exceptions\AuthorizationException;
use Nuwave\Lighthouse\Exceptions\DefinitionException;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ManagementAccessDirective extends BaseDirective implements FieldMiddleware
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<GRAPHQL
"""
Limit field access to users that are allowed to see it.
"""
directive @managementAccess on FIELD_DEFINITION
GRAPHQL;
    }

    public function handleField(FieldValue $fieldValue, Closure $next): FieldValue
    {
        $originalResolver = $fieldValue->getResolver();

        $fieldValue->setResolver(function ($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo) use ($originalResolver) {
            /** @var User|null $user */
            $user = $context->user();

            if (
                ! ($user && $user->is_manager || ($user->wca_id && $user->wca_id === $root->wca_id))
            ) {
                throw new AuthorizationException(
                    "You are not authorized to access {$this->nodeName()}"
                );
            }

            return $originalResolver($root, $args, $context, $resolveInfo);
        });

        return $next($fieldValue);
    }
}
