<?php

declare(strict_types=1);

namespace App\Repository\Datatable;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use ReflectionException;
use UnexpectedValueException;

class DatatableConfigJoin
{
    private ?self $parent = null;
    private ArrayCollection $children;
    private ?string $className = null;
    private ?DatatableConfig $datatableConfig;
    private ?string $targetClassName = null;

    #[Pure]
    public function __construct(
        private string  $field,
        private ?string $alias = null,
        private string  $aliasClassName = DatatableRepository::DEFAULT_ENTITY_ALIAS
    )
    {
        $this->alias = $field;
        $this->children = new ArrayCollection();
    }

    /**
     * @return string
     * @throws ReflectionException|UnexpectedValueException
     */
    public function isValidJoin(): string
    {
        $reflectionClass = new ReflectionClass($this->className);
        $getter = 'get' . ucfirst($this->field);

        if ($reflectionClass->hasMethod($getter)) {
            $name = $reflectionClass->getMethod($getter)->getReturnType()->getName();
            if ($name === Collection::class) {
                foreach ($reflectionClass->getProperty($this->field)->getAttributes() as $attribute) {
                    if (in_array($attribute->getName(), [OneToMany::class, ManyToMany::class], true)) {
                        return $attribute->getArguments()['targetEntity'];
                    }
                }
            }

            if (class_exists($name)) {
                $this->targetClassName = $name;
                return $name;
            }
        }

        throw new UnexpectedValueException('La class ' . $this->className . ' ne peut pas avoir de jointure sur ' . $this->field);
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
     * @return DatatableConfigJoin
     */
    public function setField(string $field): DatatableConfigJoin
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     * @return DatatableConfigJoin
     */
    public function setAlias(string $alias): DatatableConfigJoin
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @return DatatableConfigJoin|null
     */
    public function getParent(): ?DatatableConfigJoin
    {
        return $this->parent;
    }

    /**
     * @param DatatableConfigJoin|null $parent
     * @return DatatableConfigJoin
     */
    public function setParent(?DatatableConfigJoin $parent): DatatableConfigJoin
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return ArrayCollection<self>
     */
    public function getChildren(): ArrayCollection
    {
        return $this->children;
    }

    /**
     * @throws ReflectionException
     */
    public function addChildren(self $child)
    {
        if (!$this->children->contains($child) && $this->isValidJoin()) {
            $child->setParent($this);
            $child->setClassName($this->isValidJoin());
            $child->setAliasClassName($this->alias);
            if ($child->isValidJoin()) {
                $this->children->add($child);
            }
        }
    }

    /**
     * @return string|null
     */
    public function getClassName(): ?string
    {
        return $this->className;
    }

    /**
     * @param string|null $className
     * @return DatatableConfigJoin
     */
    public function setClassName(?string $className): DatatableConfigJoin
    {
        $this->className = $className;
        return $this;
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
     * @return DatatableConfigJoin
     */
    public function setDatatableConfig(?DatatableConfig $datatableConfig): DatatableConfigJoin
    {
        $this->datatableConfig = $datatableConfig;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTargetClassName(): ?string
    {
        return $this->targetClassName;
    }

    /**
     * @param string|null $targetClassName
     * @return DatatableConfigJoin
     */
    public function setTargetClassName(?string $targetClassName): DatatableConfigJoin
    {
        $this->targetClassName = $targetClassName;
        return $this;
    }

    /**
     * @return string
     */
    public function getAliasClassName(): string
    {
        return $this->aliasClassName;
    }

    /**
     * @param string $aliasClassName
     * @return DatatableConfigJoin
     */
    public function setAliasClassName(string $aliasClassName): DatatableConfigJoin
    {
        $this->aliasClassName = $aliasClassName;
        return $this;
    }
}