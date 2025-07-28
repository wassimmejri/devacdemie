<?php

class __Mustache_e0f0546d038a946a742740fa7627cb48 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="showmore-container collapsed ';
        $blockFunction = $context->findInBlock('extraclasses');
        if (is_callable($blockFunction)) {
            $buffer .= call_user_func($blockFunction, $context);
        } else {
            $value = $this->resolveValue($context->find('extraclasses'), $context);
            $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        }
        $buffer .= '" data-region="showmore">
';
        $buffer .= $indent . '    <button
';
        $buffer .= $indent . '        type="button"
';
        $buffer .= $indent . '        class="btn btn-sm btn-link p-0 ';
        $blockFunction = $context->findInBlock('buttonextraclasses');
        if (is_callable($blockFunction)) {
            $buffer .= call_user_func($blockFunction, $context);
        } else {
            $value = $this->resolveValue($context->find('buttonextraclasses'), $context);
            $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        }
        $buffer .= '"
';
        $buffer .= $indent . '        aria-expanded="false"
';
        $buffer .= $indent . '        data-action="toggle-content"
';
        $buffer .= $indent . '    >
';
        $buffer .= $indent . '        <span class="collapsed-content"> ';
        $value = $context->find('str');
        $buffer .= $this->section6e5941e7a7c6c48b1353e8561b1f4b46($context, $indent, $value);
        $buffer .= ' ';
        $value = $context->find('pix');
        $buffer .= $this->sectionEce766800de4730c5a948801df414738($context, $indent, $value);
        $buffer .= ' </span>
';
        $buffer .= $indent . '        <span class="expanded-content"> ';
        $value = $context->find('str');
        $buffer .= $this->section9020caab003c753474b06892658678d5($context, $indent, $value);
        $buffer .= ' ';
        $value = $context->find('pix');
        $buffer .= $this->section28c9785eedc49a8c6514ea2fda435979($context, $indent, $value);
        $buffer .= ' </span>
';
        $buffer .= $indent . '    </button>
';
        $buffer .= $indent . '    <div class="collapsed-content ';
        $blockFunction = $context->findInBlock('collapsedextraclasses');
        if (is_callable($blockFunction)) {
            $buffer .= call_user_func($blockFunction, $context);
        } else {
            $value = $this->resolveValue($context->find('collapsedextraclasses'), $context);
            $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        }
        $buffer .= '">
';
        $buffer .= $indent . '        ';
        $blockFunction = $context->findInBlock('collapsedcontent');
        if (is_callable($blockFunction)) {
            $buffer .= call_user_func($blockFunction, $context);
        } else {
            $value = $this->resolveValue($context->find('collapsedcontent'), $context);
            $buffer .= ($value === null ? '' : $value);
        }
        $buffer .= '
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '    <div class="expanded-content ';
        $blockFunction = $context->findInBlock('expandedextraclasses');
        if (is_callable($blockFunction)) {
            $buffer .= call_user_func($blockFunction, $context);
        } else {
            $value = $this->resolveValue($context->find('expandedextraclasses'), $context);
            $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        }
        $buffer .= '">
';
        $buffer .= $indent . '        ';
        $blockFunction = $context->findInBlock('expandedcontent');
        if (is_callable($blockFunction)) {
            $buffer .= call_user_func($blockFunction, $context);
        } else {
            $value = $this->resolveValue($context->find('expandedcontent'), $context);
            $buffer .= ($value === null ? '' : $value);
        }
        $buffer .= '
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</div>
';
        $value = $context->find('js');
        $buffer .= $this->section06b156462b4a2c71b0cb9ee3f06d5549($context, $indent, $value);

        return $buffer;
    }

    private function section6e5941e7a7c6c48b1353e8561b1f4b46(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' showmore, core ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' showmore, core ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionEce766800de4730c5a948801df414738(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' t/expandedchevron, core ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' t/expandedchevron, core ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9020caab003c753474b06892658678d5(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' showless, core ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' showless, core ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section28c9785eedc49a8c6514ea2fda435979(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' t/collapsedchevron_up, core ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' t/collapsedchevron_up, core ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section06b156462b4a2c71b0cb9ee3f06d5549(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
require([\'core/showmore\'], function(showmore) {
    showmore.init();
});
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . 'require([\'core/showmore\'], function(showmore) {
';
                $buffer .= $indent . '    showmore.init();
';
                $buffer .= $indent . '});
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
