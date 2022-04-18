<?php

namespace App\Providers;

use App\Enums\FinancialEntryType;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Enums\ShirtSize;
use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\Schema\TypeRegistry;
use App\GraphQL\Schema\Types\NativeEnumType;

class GraphQLServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param \Nuwave\Lighthouse\Schema\TypeRegistry $typeRegistry
     * @return void
     * @throws \Nuwave\Lighthouse\Exceptions\DefinitionException
     */
    public function boot(TypeRegistry $typeRegistry): void
    {
        $typeRegistry->register(new NativeEnumType(PaymentStatus::class));
        $typeRegistry->register(new NativeEnumType(RegistrationStatus::class));
        $typeRegistry->register(new NativeEnumType(FinancialEntryType::class));
        $typeRegistry->register(new NativeEnumType(ShirtSize::class));
    }
}
