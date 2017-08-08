# Introduction

Rule engine is a symfony bundle that allows you to build complex condition sets using parameters and evaluators that you define to match your objects and needs. It offers a friendly front end interface and simple integration with Doctrine entities and their corresponding Sonata admins.
A demo site?    

## Installation

You can install it through composer.
```json
 { 
   "require":{
       "zitec/rule-engine-bundle": "v1.0.0"
    }
  }  
```
# Usage

## How to use

#### Context object

Write a class implementing RuleContextInterface, and add some methods that will return the parameters you wish to include in your rules. To make things super-easy, a basic context class already exists: ContextBase. It already offers getters for 3 parameters: current_date, current_day, and current_time. Your custom context can extend this.
   
```php
namespace MyBundle/RuleEngine/Context;

use Zitec/RuleEngineBundle/Service/ContextBase;

class MyFirstContext extends ContextBase
 {
    protected $dataSource;
 
    // Using a custom name will help you identify the context in built expressions.
    public function getContextObjectKey(): string
    {
        return 'my-name';
    }
    
    // The context will need a data source to read from. We will set the data source when we evaluate the expression.
    public function setMyDataSource($dataSource)
    {
        $this->dataSource = $dataSource;
    }
    
    // Added a parameter.
    public function getMyParameter(): string
    {
        return $this->dataSource->getTheData();
    }
 }

```

#### Conditions

For each parameter (method) in the context object, create a Condition object. The condition services for the three date.time parameters supported by the ContextBase class are already defined in the rule engine bundle. We'll add another one in our new bundle for the added parameter (my_parameter).
We can write a condition from scratch, but it's much easier to extend one of the two Abstract condition classes that offer support for basic operators for single-value and array parameters. If "my_parameter" is in fact an array, we can do somthing like this:
  
```php
namespace MyBundle/RuleEngine/Conditions;

use Zitec/RuleEngineBundle/Conditions/AbstractArrayCondition;

class MyParameter extends AbstractArrayCondition
{
    // When we ask the context object for the method for this condition name, 
    // it should respond with getMyCondition, since the ContextBase does simple snake_case to getCamelCase.
    protected $name = 'my_parameter';
    // This is the name the users will see in admin pages.
    protected $label = 'My Parameter';
    // A help text displayed in the condition builder if the user selects this parameter.
    protected $description = 'Set conditions for my parameter';
    
    // Here is where we decide on the operators that will be available for this parameter.
    protected function getOperatorDefinitions(): array
    {
        $options = [
            ['key' => 'one', 'label' => 'Option one'],
            ['key' => 'two', 'label' => 'Option two'],
        ];
        
        // Details about the operator definition structure can be found in the Operator definition section.
        return [
            [
                'label'     => 'match ANY of the following',
                'name'      => $this::INTERSECTING,
                'fieldType' => 'select',
                'fieldOptions' => [
                    'multiple'  => true,
                    'options'   => $options,
                ],
            ],
            [
                'label'     => 'match NONE of the following',
                'name'      => $this::DISJOINT,
                // Details about the autocomplete feature in the Autocomplete section of this readme.
                'fieldType' => 'autocomplete',
                'fieldOptions' => [
                    'autocomplete' => 'my_autocomplete_key',
                ],
            ],
        ];
    
    }
}
```

#### Rule manager service

Now that we have our context object and the matching definitions, let's create a service using RuleConditionsManager.
 We need to give it your context object as argument and the conditions as ‘addSupportedCondition’ calls. Since we extended the ContextBase class, we can use the datetime conditions defined by rule engine too.
 Below is an example of services defined using the classes above.
 
```yaml
my_bundle.rule_engine.context.my_first_context:
    class: MyBundle\RuleEngine\Context\MyFirstContext
    shared: false

my_bundle.rule_engine.condition.my_parameter:
    class: MyBundle\RuleEngine\Conditions\MyParameter
    public: false
    
my_bundle.rule_engine.manager.my_object:
    class: Zitec\RuleEngineBundle\Service\RuleConditionsManager
    arguments: ['@my_bundle.rule_engine.context.my_first_context']
    calls:
        - [addSupportedCondition, ["@rule_engine.condition.current_date"]]
        - [addSupportedCondition, ["@rule_engine.condition.current_day"]]
        - [addSupportedCondition, ["@rule_engine.condition.current_time"]]
        - [addSupportedCondition, ["@my_bundle.rule_engine.condition.my_parameter"]]
```

#### Form building

Now we need to use the context and conditions to render the front end conditions builder. This can be done by adding a RuleEngineType form type to a form and setting the rule manager service on the 'rule_manager' key in the field options.

But in most probability, the actions you want to associate with the rules need some data of their own. For instance, we could decide to send emails to various addresses based on the parameters in our object. In that case, we can define a doctrine entity with an email field, to define the addresses, and set it as a rule entity.
Doing that is very simple: just add "implements RuleInterface" to your class, and add a "use RuleTrait" statement to actually implement the interface.

```php
class EmailAddress implements RuleInterface
{
    use RuleTrait;
    
    // Your entity's properties and getters/setters follow. 
}
```

Update the doctrine schema and notice the brand new relation with the Rule entity that will hold the expressions for your EmailAddress entity.

Your rule-integrated entity will need to be associated with a context and conditions set, i.e. a rule conditions manager. To do that, you have to add a tag to the conditions manager service declaration. The service above becomes:
```yaml
my_bundle.rule_engine.manager.my_object:
    class: Zitec\RuleEngineBundle\Service\RuleConditionsManager
    arguments: ['@my_bundle.rule_engine.context.my_first_context']
    calls:
        - [addSupportedCondition, ["@rule_engine.condition.current_date"]]
        - [addSupportedCondition, ["@rule_engine.condition.current_day"]]
        - [addSupportedCondition, ["@rule_engine.condition.current_time"]]
        - [addSupportedCondition, ["@my_bundle.rule_engine.condition.my_parameter"]]
    tags:
        - { name: rule_engine.conditions_manager, entity: "MyBundle:EmailAddress" }
```

Onwards to the admin section.

I will assume that you are using SonataAdmin to manage your doctrine entity. If so, this is what you need to do:

In the Admin class, add a "use RuleAdminTrait" statement, and use the relevant methods:

```php
class EmailAddressAdmin extends AbstractAdmin
{
    use RuleAdminTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        // Add the rule admin, using the method from the trait:
        $this->addRuleFormElement($formMapper);
        // Add the rest of your fields.
    }

    protected function configureListFields(ListMapper $list)
    {
        // Add the columns from the rule entity. On dev environments, the generated espression will also be visible.
        $this->addRuleListColumns($list);
        // Add the rest of your columns and actions.
    }
```

Congratulations! You can now see it in action and set addresses for various cases, using complex rules!

#### Rule evaluation

Somewhere in your business flow you will need to extract the email address(es) that match your object. The code for that will use the RuleEvaluator service and could look something like this:

```php
use Doctrine\ORM\EntityRepository;
use MyBundle\RuleEngine\Context\MyFirstContext;
use Zitec\RuleEngineBundle\Service\RuleEvaluator;

class RecipientChooserService
{
    /**
     * The entity repository for your EmailAddress entity
     * @var EntityRepository
     */
    protected $emailAddressRepository;

    /**
     * @var RuleEvaluator
     */
    protected $evaluator;

    /**
     * @var MyFirstContext
     */
    protected $context;

    public function __construct(
        EntityRepository $emailAddressRepository,
        RuleEvaluator $evaluator,
        MyFirstContext $context
    ) {
        $this->emailAddressRepository = $emailAddressRepository;
        $this->evaluator = $evaluator;
        $this->context = $context;
    }

    public function getRecipientAddresses($myDataSource)
    {
        // Load and filter email addresses.
        $this->context->setMyDataSource($myDataSource);
        /** @var EmailAddress[] $emailAddresses */
        $emailAddresses = $this->emailAddressRepository->findAll();

        // Determine the applicable addresses.
        $recipients = [];
        foreach ($emailAddresses as $entity) {
            if ($this->evaluator->evaluate($entity->getRule(), $this->context)) {
                $recipients[] = $entity->getEmail();
            }
        }

        return $recipients;
    }
}

```

That's it! Done!


## Additional documentation:

#### Operator definition

An operator is an array with these keys:
* name (mandatory): the machine name, used to identify the selected operator in a condition
* label (mandatory): the text that the user sees in the admin pages
* fieldType (mandatory): see possible values below
* fieldOptions (optional): see details below
* value_transform (optional): a callback to apply to the value received from the rule builder before generating the expression
* value_view_transform (optional): a callback to apply to the value received from the rule builder before generating the rule admin view

#### Field types and options

* text: basic text input, useful for single-value parameters with free-form values.
* datetime: datepicker input; add your datepicker options to the fieldOptions array on the datetimepicker key.
* interval: basic interval definition, with a text input for the "from" and "to" values.
* datetime_interval: combine the two types above and voila: interval with datetime picker.
* select: a select2 augmented select; you can see an implementation in the MyParameter condition declaration.
* autocomplete: a select2 input with autocomplete using RuleEngine's autocomplete functionality.

#### Autocomplete support:

In order to use an autocomplete field, you need to define a data source by implementing the Zitec\RuleEngineBundle\Autocomplete\AutocompleteInterface.
If you want to have an autocomplete of doctrine entities, you can extend the AbstractAutocompleteEntity class:

```php
use MyBundle\Entity\MyEntity;
use Zitec\RuleEngineBundle\Autocomplete\AbstractAutocompleteEntity;

class MyEntityAutocomplete extends AbstractAutocompleteEntity
{
    protected function getEntityClass(): string
    {
        return MyEntity::class;
    }

    protected function getIdField(): string
    {
        return 'id'; // This will be the value used in the built expression
    }

    protected function getTextField(): string
    {
        return 'name'; // This will be the value displayed to the user.
    }
}
```

and declare the service using a "rule_engine.autocomplete.data_source" tag:

```yaml
my_bundle.rule_engine.autocomplete.my_autocomplete:
    class: MyBundle\RuleEngine\Autocomplete\MyEntityAutocomplete
    arguments: ["@doctrine.orm.default_entity_manager"]
    tags:
        - { name: rule_engine.autocomplete.data_source, key: my_autocomplete_key }
```

The value for the key is what you have to use in the fieldOptions for the autocomplete type.

Don't forget to add the routing info in the routing.yml file of your app:
```yaml
rule_engine:
    resource: "@ZitecRuleEngineBundle/Resources/config/routing.yml"
    prefix:   /
```
