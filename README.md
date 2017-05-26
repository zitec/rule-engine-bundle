# Introduction

Rule engine is a symfony bundle that allows you to build complex condition sets using parameters and evaluators that you define to match your objects and needs. It offers a friendly front end interface and simple integration with Doctrine entities and their corresponding Sonata admins.
A demo site?    

## Installation

You can install it through composer.
```
 { 
   "require":{
       "zitec/rule-engine": "v1.0.0"
    }
  }  
```
# Usage

## How to use

1. Write a class implementing RuleContextInterface, and add some methods that will return the parameters you wish to include in your rules.
   
```


```

2. For each parameter (method) in the context object, create a Condition object. 
Extend abstracts or create your own from scratch.

3. Create a service using RuleConditionsManager and give it your context object as argument and the conditions as ‘addSupportedCondition’ calls.

4. Just using the field? Then set the above service to the fieldOptions, ‘rule_manager’ key.

5. Your entity: use RuleTrait and implement RuleInterface
Your entity’s admin: use RuleAdminTrait and use the relevant methods;

6. Evaluation: using the Rule entity? Use RuleEvaluator.

7. Optional: do you want to use autocomplete fields? The Autocomplete manager.
