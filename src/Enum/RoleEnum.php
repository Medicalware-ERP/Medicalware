<?php

declare(strict_types=1);

namespace App\Enum;


class RoleEnum
{
    public const ROLE_DOCTOR           = 'ROLE_DOCTOR';
    public const ROLE_HUMAN_RESOURCE   = 'ROLE_HUMAN_RESOURCE';
    public const ROLE_SECRETARY        = 'ROLE_SECRETARY';
    public const ROLE_ACCOUNTANT       = 'ROLE_ACCOUNTANT';
    public const ROLE_ADMIN_STOCK      = 'ROLE_ADMIN_STOCK';
    public const ROLE_ADMIN_SERVICES   = 'ROLE_ADMIN_SERVICES';

    public static function getChoiceList(): array
    {
        return [
            'Docteur'                   => self::ROLE_DOCTOR,
            'Ressources humaine'        => self::ROLE_HUMAN_RESOURCE,
            'Comptable'                 => self::ROLE_ACCOUNTANT,
            'Gestionnaire du stock'     => self::ROLE_ADMIN_STOCK,
            'Gestionnaire des services' => self::ROLE_ADMIN_SERVICES,
        ];
    }

    public static function getRolesByProfession(string $profession): array
    {
        return match ($profession) {
            UserTypeEnum::DOCTOR => [self::ROLE_DOCTOR],
            UserTypeEnum::HR_ASSISTANT, UserTypeEnum::HR_MANAGER, UserTypeEnum::HR_DIRECTOR => [self::ROLE_HUMAN_RESOURCE],
            UserTypeEnum::STOCK_MANAGER => [self::ROLE_ADMIN_STOCK],
            UserTypeEnum::ACCOUNTANT => [self::ROLE_ACCOUNTANT],
            UserTypeEnum::HEAD_OF_SERVICE => [self::ROLE_ADMIN_SERVICES],
            default => []
        };
    }
}