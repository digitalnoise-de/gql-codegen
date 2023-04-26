<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator\Php81;

use GraphQLGenerator\Generator\TypeDetails;
use GraphQLGenerator\Type\ExistingClassType;
use GraphQLGenerator\Type\GeneratedClassType;
use GraphQLGenerator\Type\ListType;
use GraphQLGenerator\Type\NonNullable;
use GraphQLGenerator\Type\ScalarType;
use GraphQLGenerator\Type\Type;

final class TypeDetailsFactoryForPhp81
{
    public static function create(Type $type): TypeDetails
    {
        return self::typeDetails($type, true);
    }

    private static function typeDetails(Type $type, bool $nullable): TypeDetails
    {
        if ($type instanceof NonNullable) {
            return self::typeDetails($type->elementType, false);
        }

        if ($type instanceof ListType) {
            $innerType    = self::typeDetails($type->elementType, true);
            $listItemType = $innerType->nullable ? sprintf('%s|null', $innerType->phpType) : $innerType->phpType;
            $listType     = $nullable ? sprintf('list<%s>|null', $listItemType) : sprintf('list<%s>', $listItemType);

            return new TypeDetails('array', $nullable, $listType);
        }

        if ($type instanceof GeneratedClassType) {
            return new TypeDetails('\\' . $type->className, $nullable, null);
        }

        if ($type instanceof ExistingClassType) {
            return new TypeDetails('\\' . $type->className, $nullable, null);
        }

        if ($type instanceof ScalarType) {
            if ($type->equals(ScalarType::STRING())) {
                return new TypeDetails('string', $nullable, null);
            }

            if ($type->equals(ScalarType::BOOLEAN())) {
                return new TypeDetails('bool', $nullable, null);
            }

            if ($type->equals(ScalarType::INTEGER())) {
                return new TypeDetails('int', $nullable, null);
            }

            if ($type->equals(ScalarType::FLOAT())) {
                return new TypeDetails('float', $nullable, null);
            }
        }

        throw new \LogicException(sprintf('Unexpected type "%s"', $type::class));
    }
}
