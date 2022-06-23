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
            new EventType(self::LEAVE, "Congé", "Congé non payé", "#3199ff"),
            new EventType(self::PAID_LEAVE, "Congé payé", "Congé payé", "#0080ff"),
            new EventType(self::SENIORITY_LEAVE, "Congé d'ancienneté", "Congé d'ancienneté", "#297bcc"),
            new EventType(self::MEDICAL_LEAVE, "Congé médical", "Congé lié à une raison médical", "#055fb8"),
            new EventType(self::SPECIAL_LEAVE, "Congé spécial (mariage, enterrement ect...)", "Congé lié à une raison spéciale", "#4475a5"),
            new EventType(self::SICK_LEAVE, "Arrêt maladie", "Arrêt maladie", "#5e84ff"),
            new EventType(self::RDV, "Rendez-vous", "Rendez-vous", "#ffb267"),
            new EventType(self::FORMATION, "Formation", "Formation", "#8fce00"),
            new EventType(self::CONSULTATION, "Consultation", "Consultation", "#ce7e00"),
            new EventType(self::BUSINESS_TRIP, "Déplacement professionnel", "Déplacement professionnel", "#674ea7"),
            new EventType(self::PRIVATE, "Privé", "Privé", "#42af9d"),
            new EventType(self::PERSONAL, "Personnel", "Personnel", "#8e51f2")
        ];
    }

    public function getEnum(): string
    {
        return EventType::class;
    }
}