<?php

class __Mustache_b515e413e349684ca7fdc9aa4568e5fd extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $value = $context->find('node');
        $buffer .= $this->section9b48463902fb2671b5dda40324e2a511($context, $indent, $value);

        return $buffer;
    }

    private function sectionE4b08b5d95fc9192ab15a617ff292a0c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <li><a href="{{{action}}}">{{text}}</a></li>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <li><a href="';
                $value = $this->resolveValue($context->find('action'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '">';
                $value = $this->resolveValue($context->find('text'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</a></li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionDbc6888f8cc9a8aa623e32c7282bf31f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                {{#display}}
                    <li><a href="{{{action}}}">{{text}}</a></li>
                {{/display}}
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('display');
                $buffer .= $this->sectionE4b08b5d95fc9192ab15a617ff292a0c($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9b48463902fb2671b5dda40324e2a511(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
<div class="container-fluid">
    <div class="row">
        <ul class="list-unstyled ms-0">
            {{#node.children}}
                {{#display}}
                    <li><a href="{{{action}}}">{{text}}</a></li>
                {{/display}}
            {{/node.children}}
        </ul>
    </div>
</div>
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '<div class="container-fluid">
';
                $buffer .= $indent . '    <div class="row">
';
                $buffer .= $indent . '        <ul class="list-unstyled ms-0">
';
                $value = $context->findDot('node.children');
                $buffer .= $this->sectionDbc6888f8cc9a8aa623e32c7282bf31f($context, $indent, $value);
                $buffer .= $indent . '        </ul>
';
                $buffer .= $indent . '    </div>
';
                $buffer .= $indent . '</div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
