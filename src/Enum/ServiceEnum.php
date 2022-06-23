<?php

declare(strict_types=1);

namespace App\Enum;


use App\Entity\Service;
use JetBrains\PhpStorm\Pure;

class ServiceEnum implements DataInitializerInterface
{
    public const CARDIOLOGIE = 'Cardiologie';
    public const IMMUNOLOGIE = 'Immunologie';
    public const RADIOLOGIE = 'Radiologie';
    public const CHIRURGIE  = 'Chirurgie';
    public const NEUROLOGIE  = 'Neurologie';
    public const PNEUMOLOGIE  = 'Pneumologie';
    public const ODONTOLOGIE  = 'Odontologie';
    public const DERMATOLOGIE  = 'Dermatologie';
    public const TRAUMATOLOGIE  = 'Traumatologie';
    public const MEDECINE_INTERNE  = 'Medecine interne';
    public const ENDOCRINOLOGIE  = 'Endocrinologie';
    public const ANATOMOPATHOLOGIE  = 'Anatomopathologie';
    public const HEMATOLOGIE  = 'Hematologie';
    public const GASTRO_ENTEROLOGIE  = 'Gastro-entérologie';
    public const UROLOGIE  = 'Urologie';
    public const PHARMACIE  = 'Pharmacie';
    public const PEDIATRIE  = 'Pediatrie';

    #[Pure]
    public function getData(): array
    {
        return [
            new Service(self::ANATOMOPATHOLOGIE, "Anatomopathologie"),
            new Service(self::CARDIOLOGIE, "Cardiologie"),
            new Service(self::CHIRURGIE, "Chirurgien"),
            new Service(self::DERMATOLOGIE, "Dermatologie"),
            new Service(self::ENDOCRINOLOGIE, "Endocrinologie"),
            new Service(self::GASTRO_ENTEROLOGIE, "Gastro-entérologie"),
            new Service(self::HEMATOLOGIE, "Hematologie"),
            new Service(self::IMMUNOLOGIE, "Immunologie"),
            new Service(self::MEDECINE_INTERNE, "Medecine interne"),
            new Service(self::ODONTOLOGIE, "Odontologie"),
            new Service(self::NEUROLOGIE, "Neurologie"),
            new Service(self::PEDIATRIE, "Pediatrie"),
            new Service(self::PHARMACIE, "Pharmacie"),
            new Service(self::UROLOGIE, "Urologie"),
            new Service(self::RADIOLOGIE, "Radiologie"),
            new Service(self::TRAUMATOLOGIE, "Traumatologie"),
            new Service(self::PNEUMOLOGIE, "Pneumologie")
        ];
    }

    public function getEnum(): string
    {
        return Service::class;
    }
}