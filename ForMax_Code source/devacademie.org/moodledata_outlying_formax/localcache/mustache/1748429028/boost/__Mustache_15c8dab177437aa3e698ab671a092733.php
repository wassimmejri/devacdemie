<?php

class __Mustache_15c8dab177437aa3e698ab671a092733 extends Mustache_Template
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
        $buffer .= $this->sectionC6561da3e520435f9290ce54ae98bd10($context, $indent, $value);

        return $buffer;
    }

    private function sectionC6561da3e520435f9290ce54ae98bd10(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
require([\'mod_assign/grading_panel\'], function(GradingPanel) {
    new GradingPanel(\'[data-region="grade"]\');
});
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . 'require([\'mod_assign/grading_panel\'], function(GradingPanel) {
';
                $buffer .= $indent . '    new GradingPanel(\'[data-region="grade"]\');
';
                $buffer .= $indent . '});
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
