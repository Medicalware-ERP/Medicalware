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
            'Secretaire'                => self::ROLE_SECRETARY,
            'Comptable'                 => self::ROLE_ACCOUNTANT,
            'Gestionnaire du stock'     => self::ROLE_ADMIN_STOCK,
            'Gestionnaire des services' => self::ROLE_ADMIN_SERVICES,
        ];
    }
}