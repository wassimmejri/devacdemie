<?php

class __Mustache_d3c3388f6fa8386de1d02095f8705a8d extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $value = $this->resolveValue($context->find('header'), $context);
        $buffer .= $indent . ($value === null ? '' : $value);
        $buffer .= '
';
        $value = $context->find('hasitems');
        $buffer .= $this->sectionD979133c20ba8bfb90de2e4a4a796d28($context, $indent, $value);

        return $buffer;
    }

    private function sectionE7e6e6ecd2f53ba078bf45fd8062155b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' id="availability-tree-{{id}}" ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' id="availability-tree-';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1bd7830f5a58d844498c388d9199043d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <li>
            {{> core_availability/availability_info }}
        </li>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <li>
';
                if ($partial = $this->mustache->loadPartial('core_availability/availability_info')) {
                    $buffer .= $partial->renderInternal($context, $indent . '            ');
                }
                $buffer .= $indent . '        </li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD979133c20ba8bfb90de2e4a4a796d28(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
<ul {{#id}} id="availability-tree-{{id}}" {{/id}} data-region="availability-multiple">
    {{#items}}
        <li>
            {{> core_availability/availability_info }}
        </li>
    {{/items}}
</ul>
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '<ul ';
                $value = $context->find('id');
                $buffer .= $this->sectionE7e6e6ecd2f53ba078bf45fd8062155b($context, $indent, $value);
                $buffer .= ' data-region="availability-multiple">
';
                $value = $context->find('items');
                $buffer .= $this->section1bd7830f5a58d844498c388d9199043d($context, $indent, $value);
                $buffer .= $indent . '</ul>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
