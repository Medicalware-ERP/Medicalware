<?php

namespace App\Form\Extension;

use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;

class SetFormNameExtension extends AbstractTypeExtension
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        if (is_object($builder->getForm()->getData()))  {
            $builder->add('referer', HiddenType::class, [
                'mapped' => false,
                'data' => $this->requestStack->getCurrentRequest()->headers->get('referer')
            ]);
        }
    }

    #[NoReturn]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $formName = $options['form_name'] ?? null;
        $formId = $options['form_id'] ?? null;

        if (!is_string($formName) || !is_string($formId)) {
            return;
        }

        $view->vars['name'] = $formName;
        $view->vars['attr']['id'] = $formId;

    }

    public static function getExtendedTypes(): iterable
    {
        return [
            FormType::class
        ];
    }
}