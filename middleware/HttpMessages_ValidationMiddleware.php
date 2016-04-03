<?php

namespace Craft;

use Craft\HttpMessages_CraftRequest as Request;
use Craft\HttpMessages_CraftResponse as Response;
use Respect\Validation\Validator;
use Respect\Validation\Exceptions\NestedValidationException;

class ValidationMiddleware
{
    /**
     * Invoke
     *
     * @return void
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $config = $request->getRoute()->getMiddlewareConfig('validation');
        $input = ($request->getMethod() === 'GET') ? $request->getQueryParams() : $request->getParams();

        try {
            $validator = $this->getValidator($config);

            $validator->assert($input);
        } catch(NestedValidationException $validation_exception) {
            $exception = new HttpMessages_Exception('Validation error.');

            $exception->setErrors($validation_exception->getMessages());

            $exception->setInput($input);

            throw $exception;
        }

        $response = $next($request, $response);

        return $response;
    }

    /**
     * Get Validator
     *
     * @param array $config Config
     *
     * @return Validator Validator
     */
    private function getValidator(array $config)
    {
        $validator = new Validator;

        $validator = $this->addRulesFromConfig($validator, $config);

        return $validator;
    }

    /**
     * Add Rules From Config
     *
     * @param Validator $validator Validator
     * @param array     $config    Config
     */
    private function addRulesFromConfig(Validator $validator, array $config)
    {
        foreach ($config as $key => $rules) {
            $validator = $this->processRules($validator, $key, $rules);
        }

        return $validator;
    }

    /**
     * Process Rules
     *
     * @param Validator $validator Validator
     * @param string    $key       Key
     * @param string    $rules     Rules
     *
     * @return Validator
     */
    private function processRules(Validator $validator, $key, $rules)
    {
        $rules = explode('|', $rules);

        foreach ($rules as $rule) {
            $validator = $this->processRule($validator, $key, $rule);
        }

        return $validator;
    }

    /**
     * Process Rule
     *
     * @param Validator $validator Validator
     * @param string    $key       Key
     * @param rule      $rule      Rule
     *
     * @return Validator
     */
    private function processRule(Validator $validator, $key, $rule)
    {
        $required = true;

        if (!$this->isRuleRequired($rule)) {
            $rule = substr($rule, 1);

            $required = false;
        }

        if ($rule_arguments = $this->getRuleArguments($rule)) {
            $class = '\\Respect\\Validation\\Validator::' . $rule_arguments['rule'];

            return $validator->keyNested($key, call_user_func_array($class, $rule_arguments['arguments']), $required);
        }

        return $validator->keyNested($key, Validator::$rule(), $required);
    }

    /**
     * Is Rule Required
     *
     * @param string $rule Rule
     *
     * @return boolean
     */
    private function isRuleRequired($rule)
    {
        return ($rule[0] !== '?');
    }

    /**
     * Get Rule Arguments
     *
     * @param string $rule Rule
     *
     * @return array Rule Arguments
     */
    private function getRuleArguments($rule)
    {
        $regex = '#(.*)\((([^()]+|(?R))*)\)#';

        if (preg_match_all($regex, $rule, $matches)) {
            $rule = $matches[1][0];
            $arguments = $matches[2][0];

            $arguments = array_map('trim', explode(',', $arguments));

            return [
                'rule'      => $rule,
                'arguments' => $arguments,
            ];
        }

        return null;
    }

}
