<?php

declare(strict_types=1);

namespace App\Enum;


class RoleEnum
{
    private const ROLE_DOCTOR           = 'ROLE_DOCTOR';
    private const ROLE_HUMAN_RESOURCE   = 'ROLE_HUMAN_RESOURCE';
    private const ROLE_SECRETARY        = 'ROLE_SECRETARY';
    private const ROLE_ACCOUNTANT       = 'ROLE_ACCOUNTANT';
    private const ROLE_ADMIN_STOCK      = 'ROLE_ADMIN_STOCK';
    private const ROLE_ADMIN_SERVICES   = 'ROLE_ADMIN_SERVICES';

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