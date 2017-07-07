<?php

namespace Zitec\RuleEngineBundle\Form\Type;

use Zitec\RuleEngineBundle\Form\Transformer\RuleEngineTransformer;
use Zitec\RuleEngineBundle\Service\RuleConditionsManager;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RuleEngineType
 * The rule form type that allows building complex rules using various conditions and transforms them to an expression.
 */
class RuleEngineType extends HiddenType
{

    const PREFIX = 'rule_engine';

    protected $conditionFields = ['name' => '', 'label' => '', 'description' => '', 'operators' => ''];
    protected $operatorFields = ['name' => '', 'label' => '', 'fieldType' => '', 'fieldOptions' => ''];

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return self::PREFIX;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                // hidden fields cannot have a required attribute
                'required'       => false,
                // Pass errors to the parent
                'error_bubbling' => true,
                'compound'       => false,
                'attr'           => ['class' => 'rule_engine_input'],
                'rule_manager'   => null,
            ]
        );
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /* @var $ruleManager RuleConditionsManager */
        $ruleManager = $options['rule_manager'];
        $definitions = [];
        foreach ($ruleManager->getSupportedConditions() as $condition) {
            $definition = $condition->getDefinitions();
            $filtered = array_intersect_key($definition, $this->conditionFields);
            $filtered['operators'] = array_intersect_key($filtered['operators'], $this->operatorFields);
            $definitions[] = $filtered;
        }
        $view->vars['condition_definitions'] = json_encode($definitions);
        parent::buildView($view, $form, $options);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!isset($options['rule_manager']) || !($options['rule_manager'] instanceof RuleConditionsManager)) {
            throw new \InvalidArgumentException('Missing context object.');
        }
        $builder->addViewTransformer(new RuleEngineTransformer($options['rule_manager']));
    }
}
