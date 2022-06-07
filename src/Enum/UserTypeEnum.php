<?php

declare(strict_types=1);

namespace App\Enum;

use App\Entity\UserType;
use JetBrains\PhpStorm\Pure;

class UserTypeEnum implements DataInitializerInterface
{
    public const CEO = 'Chief Executive Officer';
    public const DIRECTOR = 'Director';
    public const PROFESSOR = 'Professor';
    public const DOCTOR = 'Doctor';
    public const CAREGIVER = 'Caregiver';
    public const NURSE = 'Nurse';
    public const HR_DIRECTOR = 'Human Resources Director';
    public const HR_MANAGER = 'Human Resources Manager';
    public const HR_ASSISTANT = 'Human Resources Assistant';
    public const STOCK_MANAGER = 'Stock Manager';
    public const ACCOUNTANT = 'Accountant';
    public const HEAD_OF_SERVICE = 'Head of Service';
    public const AREA_TECHNICIAN = 'Area Technician';

    #[Pure]
    public function getData(): array
    {
        return [
            new UserType(self::CEO, "Président Directeur Général"),
            new UserType(self::DIRECTOR, "Directeur"),
            new UserType(self::PROFESSOR, "Professeur"),
            new UserType(self::DOCTOR, "Docteur"),
            new UserType(self::CAREGIVER, "Aide Soignant"),
            new UserType(self::NURSE, "Infirmière"),
            new UserType(self::HR_DIRECTOR, "Directeur des Ressources Humaines"),
            new UserType(self::HR_MANAGER, "Responsable des Ressources Humaines"),
            new UserType(self::HR_ASSISTANT, "Assistante des Ressources Humaines"),
            new UserType(self::STOCK_MANAGER, "Gestionnaire du Stock"),
            new UserType(self::ACCOUNTANT, "Comptable"),
            new UserType(self::HEAD_OF_SERVICE, "Chef de Service"),
            new UserType(self::AREA_TECHNICIAN, "Technicien de Surface")
        ];
    }

    public function getEnum(): string
    {
        return UserType::class;
    }
}