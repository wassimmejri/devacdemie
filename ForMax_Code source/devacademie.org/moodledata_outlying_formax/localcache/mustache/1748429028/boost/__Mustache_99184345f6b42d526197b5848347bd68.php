<?php

class __Mustache_99184345f6b42d526197b5848347bd68 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<h3>';
        $value = $context->find('str');
        $buffer .= $this->section45027158e209100ea97797c9dbbead43($context, $indent, $value);
        $buffer .= ' ';
        if ($partial = $this->mustache->loadPartial('mod_assign/loading')) {
            $buffer .= $partial->renderInternal($context);
        }
        $buffer .= '</h3>
';

        return $buffer;
    }

    private function section45027158e209100ea97797c9dbbead43(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'savingchanges, mod_assign';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'savingchanges, mod_assign';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
