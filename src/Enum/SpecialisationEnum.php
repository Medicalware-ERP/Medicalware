<?php

declare(strict_types=1);

namespace App\Enum;


use App\Entity\Specialisation;
use JetBrains\PhpStorm\Pure;

class SpecialisationEnum implements DataInitializerInterface
{
    public const CARDIOLOGUE = 'Cardiologue';
    public const IMMUNOLOGUE = 'Immunologue';
    public const RADIOLOGUE = 'Radiologue';
    public const CHIRURGIEN  = 'Chirurgien';
    public const NEUROLOGUE  = 'Neurologue';
    public const PNEUMOLOGUE  = 'Pneumologue';
    public const ODONTOLOGUE  = 'Odontologue';
    public const DERMATOLOGUE  = 'Dermatologue';
    public const TRAUMATOLOGUE  = 'Traumatologue';
    public const MEDECIN_INTERNE  = 'Medecin interne';
    public const ENDOCRINOLOGUE  = 'Endocrinologue';
    public const ANATOMOPATHOLOGISTE  = 'Anatomopathologiste';
    public const HEMATOLOGUE  = 'Hematologue';
    public const GASTRO_ENTEROLOGUE  = 'Gastro-entérologue';
    public const UROLOGUE  = 'Urologue';
    public const PHARMACIEN  = 'Pharmacien';
    public const PEDIATRE  = 'Pediatre';

    #[Pure]
    public function getData(): array
    {
        return [
            new Specialisation(self::ANATOMOPATHOLOGISTE, "Anatomopathologiste"),
            new Specialisation(self::CARDIOLOGUE, "Cardiologue"),
            new Specialisation(self::CHIRURGIEN, "Chirurgien"),
            new Specialisation(self::DERMATOLOGUE, "Dermatologue"),
            new Specialisation(self::ENDOCRINOLOGUE, "Endocrinologue"),
            new Specialisation(self::GASTRO_ENTEROLOGUE, "Gastro-entérologue"),
            new Specialisation(self::HEMATOLOGUE, "Hematologue"),
            new Specialisation(self::IMMUNOLOGUE, "Immunologue"),
            new Specialisation(self::MEDECIN_INTERNE, "Medecin interne"),
            new Specialisation(self::ODONTOLOGUE, "Odontologue"),
            new Specialisation(self::NEUROLOGUE, "Neurologue"),
            new Specialisation(self::PEDIATRE, "Pediatre"),
            new Specialisation(self::PHARMACIEN, "Pharmacien"),
            new Specialisation(self::UROLOGUE, "Urologue"),
            new Specialisation(self::RADIOLOGUE, "Radiologue"),
            new Specialisation(self::TRAUMATOLOGUE, "Traumatologue"),
            new Specialisation(self::PNEUMOLOGUE, "Pneumologue")
        ];
    }

    public function getEnum(): string
    {
        return Specialisation::class;
    }
}