<?php

declare(strict_types=1);

namespace App\Enum;

use App\Entity\Planning\EventType;
use JetBrains\PhpStorm\Pure;

class EventTypeEnum implements DataInitializerInterface
{
    public const LEAVE = 'Holiday';
    public const PAID_LEAVE = 'Paid holiday';
    public const SENIORITY_LEAVE = 'Seniority leave';
    public const MEDICAL_LEAVE = 'Medical leave';
    public const SPECIAL_LEAVE = 'Special leave';
    public const SICK_LEAVE = 'Sick leave';
    public const RDV = 'Rendez-vous';
    public const FORMATION = 'Formation';
    public const CONSULTATION = 'Consultation';
    public const BUSINESS_TRIP = 'Business trip';
    public const PRIVATE = 'Private';
    public const PERSONAL = 'Personal';

    #[Pure]
    public function getData(): array
    {
        return [
            new EventType(self::LEAVE, "Congé"),
            new EventType(self::PAID_LEAVE, "Congé payé"),
            new EventType(self::SENIORITY_LEAVE, "Congé d'ancienneté"),
            new EventType(self::MEDICAL_LEAVE, "Congé médical"),
            new EventType(self::SPECIAL_LEAVE, "Congé spécial (mariage, enterrement ect...)"),
            new EventType(self::SICK_LEAVE, "Arrêt maladie"),
            new EventType(self::RDV, "Rendez-vous"),
            new EventType(self::FORMATION, "Formation"),
            new EventType(self::CONSULTATION, "Consultation"),
            new EventType(self::BUSINESS_TRIP, "Déplacement professionnel"),
            new EventType(self::PRIVATE, "Privé"),
            new EventType(self::PERSONAL, "Personnel")
        ];
    }

    public function getEnum(): string
    {
        return EventType::class;
    }
}