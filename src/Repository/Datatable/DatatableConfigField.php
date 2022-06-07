<?php

declare(strict_types=1);

namespace App\Repository\Datatable;


use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use ReflectionException;
use UnexpectedValueException;

class DatatableConfigField
{
    private ?DatatableConfig $datatableConfig = null;

    #[Pure]
    public function __construct(
        private string $field,
        private string $aliasJoinField = DatatableRepository::DEFAULT_ENTITY_ALIAS
    )
    {
    }


    /**
     * @throws ReflectionException
     */
    public function isFieldValid(): bool
    {
        $clasName = $this->datatableConfig->getClassName();

        foreach ($this->datatableConfig->getJoins() as $join) {
            if ($join->getAlias() === $this->aliasJoinField) {
                $clasName = $join->getTargetClassName();
                break;
            }
        }

        $reflectionClass = new ReflectionClass($clasName);
        if ($reflectionClass->hasProperty($this->field) || $reflectionClass->hasMethod('get' . ucfirst($this->field))) {
            return true;
        }


        throw new UnexpectedValueException(sprintf("No field found for %s in class %s for alias %s", $this->field, $clasName, $this->aliasJoinField));
    }

    /**
     * @return DatatableConfig|null
     */
    public function getDatatableConfig(): ?DatatableConfig
    {
        return $this->datatableConfig;
    }

    /**
     * @param DatatableConfig|null $datatableConfig
     * @return $this
     */
    public function setDatatableConfig(?DatatableConfig $datatableConfig): self
    {
        $this->datatableConfig = $datatableConfig;
        return $this;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function setField(string $field): self
    {
        $this->field = $field;
        return $this;
    }


    /**
     * @return string
     */
    public function getAliasJoinField(): string
    {
        return $this->aliasJoinField;
    }
}