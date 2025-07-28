<?php

class __Mustache_29674b7c45d2f22ad3bcb44a3da9ea7e extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        if ($partial = $this->mustache->loadPartial('mod_assign/loading')) {
            $buffer .= $partial->renderInternal($context);
        }
        $value = $context->find('js');
        $buffer .= $this->section462f2728b586d7696972b23d53681f42($context, $indent, $value);

        return $buffer;
    }

    private function section462f2728b586d7696972b23d53681f42(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
require([\'mod_assign/grading_navigation_user_info\'], function(UserInfo) {
    new UserInfo(\'[data-region="user-info"]\');
});
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . 'require([\'mod_assign/grading_navigation_user_info\'], function(UserInfo) {
';
                $buffer .= $indent . '    new UserInfo(\'[data-region="user-info"]\');
';
                $buffer .= $indent . '});
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
