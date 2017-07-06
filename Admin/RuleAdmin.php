<?php

namespace Zitec\RuleEngineBundle\Admin;

use Zitec\RuleEngineBundle\Form\Type\RuleEngineType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class RuleAdmin
 */
class RuleAdmin extends AbstractAdmin
{

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $fieldOptions = ['label' => false, 'template' => 'ZitecRuleEngineBundle:Form:fields.html.twig'];
        $parentOptions = $this->getParentFieldDescription()->getOptions();
        if (!isset($parentOptions['rule_manager'])) {
            throw new \RuntimeException('Missing rule manager object!');
        }
        $fieldOptions['rule_manager'] = $parentOptions['rule_manager'];
        $formMapper
            ->add('active')
            ->add('name')
            ->add('ruleObject', RuleEngineType::class, $fieldOptions)
            ->end();
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list->add('expression')
            ->add('json')
            ->add('_action', null, ['actions' => ['edit' => [], 'delete' => []]]);
    }
}
