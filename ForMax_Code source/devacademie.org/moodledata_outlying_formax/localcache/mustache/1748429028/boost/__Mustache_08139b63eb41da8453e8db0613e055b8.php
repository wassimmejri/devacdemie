<?php

class __Mustache_08139b63eb41da8453e8db0613e055b8 extends Mustache_Template
{
    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $buffer = '';

        $buffer .= $indent . '<div class="header">
';
        $value = $this->resolveValue($context->find('gradepassicon'), $context);
        $buffer .= $indent . ($value === null ? '' : $value);
        $buffer .= '
';
        $buffer .= $indent . '<span class="';
        $value = $this->resolveValue($context->find('extraclasses'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '">';
        $value = $this->resolveValue($context->find('text'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '</span>
';
        $buffer .= $indent . '</div>
';

        return $buffer;
    }
}
