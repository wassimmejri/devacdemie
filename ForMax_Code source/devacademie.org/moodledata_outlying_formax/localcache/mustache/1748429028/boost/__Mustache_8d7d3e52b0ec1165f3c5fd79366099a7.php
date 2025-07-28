<?php

class __Mustache_8d7d3e52b0ec1165f3c5fd79366099a7 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<a href="#previous" data-action="previous-user" aria-label="';
        $value = $context->find('str');
        $buffer .= $this->section56f3c59d659bc39e0adc0a7331b4c7ef($context, $indent, $value);
        $buffer .= '" title="';
        $value = $context->find('str');
        $buffer .= $this->section56f3c59d659bc39e0adc0a7331b4c7ef($context, $indent, $value);
        $buffer .= '">';
        $value = $this->resolveValue($context->find('larrow'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '</a>
';
        $buffer .= $indent . '<span data-region="input-field">
';
        $buffer .= $indent . '    <label for="change-user-select" class="sr-only">';
        $value = $context->find('str');
        $buffer .= $this->section785c6ff82974a3b8c8f85b8bfb8c4743($context, $indent, $value);
        $buffer .= '</label>
';
        $buffer .= $indent . '    <select id="change-user-select" data-action="change-user" data-currentuserid="';
        $value = $this->resolveValue($context->find('currentuserid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" data-assignmentid="';
        $value = $this->resolveValue($context->find('assignmentid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" data-groupid="';
        $value = $this->resolveValue($context->find('groupid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"></select>
';
        $buffer .= $indent . '</span>
';
        $buffer .= $indent . '<a href="#next" data-action="next-user" aria-label="';
        $value = $context->find('str');
        $buffer .= $this->sectionC8d5d85724116f871665a6cd3f2a067b($context, $indent, $value);
        $buffer .= '" title="';
        $value = $context->find('str');
        $buffer .= $this->sectionC8d5d85724116f871665a6cd3f2a067b($context, $indent, $value);
        $buffer .= '">';
        $value = $this->resolveValue($context->find('rarrow'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '</a>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '<br>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '<span data-region="user-count">
';
        $buffer .= $indent . '    <small>
';
        $buffer .= $indent . '        <span data-region="user-count-summary">';
        $value = $context->find('str');
        $buffer .= $this->section0020318c935939c95fb0d36e79b8cb7c($context, $indent, $value);
        $buffer .= '</span>
';
        $buffer .= $indent . '    </small>
';
        $buffer .= $indent . '</span>
';
        $buffer .= $indent . '<a href="#" data-region="user-filters" title="';
        $value = $context->find('str');
        $buffer .= $this->sectionBaf6094422528beb51f5cf06b766d123($context, $indent, $value);
        $buffer .= '" aria-expanded="false" aria-controls="filter-configuration-';
        $value = $this->resolveValue($context->find('uniqid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '">
';
        $buffer .= $indent . '    <span class="accesshide">
';
        $value = $context->find('filters');
        $buffer .= $this->section51c5bdfe04b6d9a48916d63c05fd8b3c($context, $indent, $value);
        $value = $context->find('filters');
        if (empty($value)) {
            
            $buffer .= $indent . '            ';
            $value = $context->find('str');
            $buffer .= $this->section91121b875b722b6742bc4932aadc1793($context, $indent, $value);
            $buffer .= '
';
        }
        $buffer .= $indent . '    </span>
';
        $buffer .= $indent . '    ';
        $value = $context->find('pix');
        $buffer .= $this->sectionFaec89d37ccdced2a9ac2bb07df6e146($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '</a>
';
        $buffer .= $indent . '<a href="#" data-region="user-resettable" title="';
        $value = $context->find('str');
        $buffer .= $this->sectionF6f379b2340519e9af59a8e1f6546c0f($context, $indent, $value);
        $buffer .= '">
';
        $buffer .= $indent . '    ';
        $value = $context->find('str');
        $buffer .= $this->sectionF6f379b2340519e9af59a8e1f6546c0f($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '</a>
';
        $buffer .= $indent . '<div data-region="configure-filters" id="filter-configuration-';
        $value = $this->resolveValue($context->find('uniqid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" class="card card-large p-2">
';
        $buffer .= $indent . '    <form>
';
        $buffer .= $indent . '        <span class="row px-3 py-1">
';
        $buffer .= $indent . '            <label class="text-end w-25 p-2 m-0" for="filter-general-';
        $value = $this->resolveValue($context->find('uniqid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '">
';
        $buffer .= $indent . '                ';
        $value = $context->find('str');
        $buffer .= $this->section11e2858d03ffd227f47f4f19b293f73e($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            </label>
';
        $buffer .= $indent . '            <select name="filter" class="custom-select w-50" id="filter-general-';
        $value = $this->resolveValue($context->find('uniqid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '">
';
        $value = $context->find('filters');
        $buffer .= $this->sectionB67615b88d9aaad175b567ed1a2a20aa($context, $indent, $value);
        $buffer .= $indent . '            </select>
';
        $buffer .= $indent . '        </span>
';
        $value = $context->find('hasmarkingallocation');
        $buffer .= $this->sectionB826aade92030c5d6d7537448b50a316($context, $indent, $value);
        $value = $context->find('hasmarkingworkflow');
        $buffer .= $this->section88305569c34b99f418cae383f64a94fc($context, $indent, $value);
        $buffer .= $indent . '    </form>
';
        $buffer .= $indent . '</div>
';

        return $buffer;
    }

    private function section56f3c59d659bc39e0adc0a7331b4c7ef(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' previoususer, mod_assign ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' previoususer, mod_assign ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section785c6ff82974a3b8c8f85b8bfb8c4743(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' changeuser, mod_assign ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' changeuser, mod_assign ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC8d5d85724116f871665a6cd3f2a067b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' nextuser, mod_assign ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' nextuser, mod_assign ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section0020318c935939c95fb0d36e79b8cb7c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'xofy, mod_assign, { "x": "{{index}}", "y": "{{count}}" }';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'xofy, mod_assign, { "x": "';
                $value = $this->resolveValue($context->find('index'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '", "y": "';
                $value = $this->resolveValue($context->find('count'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" }';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionBaf6094422528beb51f5cf06b766d123(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'changefilters, mod_assign';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'changefilters, mod_assign';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section51c5bdfe04b6d9a48916d63c05fd8b3c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            {{filtername}}
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            ';
                $value = $this->resolveValue($context->find('filtername'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section91121b875b722b6742bc4932aadc1793(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'nofilters, mod_assign';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'nofilters, mod_assign';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionFaec89d37ccdced2a9ac2bb07df6e146(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'i/filter';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'i/filter';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF6f379b2340519e9af59a8e1f6546c0f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'resettable';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'resettable';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section11e2858d03ffd227f47f4f19b293f73e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'filter, mod_assign';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'filter, mod_assign';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC877874b20aed109ed5be9bdc0ef9c49(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'selected="selected"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'selected="selected"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB67615b88d9aaad175b567ed1a2a20aa(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <option value="{{key}}" {{#active}}selected="selected"{{/active}} > {{name}} </option>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <option value="';
                $value = $this->resolveValue($context->find('key'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" ';
                $value = $context->find('active');
                $buffer .= $this->sectionC877874b20aed109ed5be9bdc0ef9c49($context, $indent, $value);
                $buffer .= ' > ';
                $value = $this->resolveValue($context->find('name'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= ' </option>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section6fe9c78e700902b90128736393bd53f7(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'markerfilter, mod_assign';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'markerfilter, mod_assign';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB826aade92030c5d6d7537448b50a316(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <span class="row px-3 py-1">
            <label class="text-end w-25 p-2 m-0" for="filter-marker-{{uniqid}}">
                {{#str}}markerfilter, mod_assign{{/str}}
            </label>
            <select name="markerfilter" class="custom-select w-50" id="filter-marker-{{uniqid}}">
            {{#markingallocationfilters}}
                <option value="{{key}}" {{#active}}selected="selected"{{/active}} > {{name}} </option>
            {{/markingallocationfilters}}
            </select>
        </span>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <span class="row px-3 py-1">
';
                $buffer .= $indent . '            <label class="text-end w-25 p-2 m-0" for="filter-marker-';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '                ';
                $value = $context->find('str');
                $buffer .= $this->section6fe9c78e700902b90128736393bd53f7($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            </label>
';
                $buffer .= $indent . '            <select name="markerfilter" class="custom-select w-50" id="filter-marker-';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $value = $context->find('markingallocationfilters');
                $buffer .= $this->sectionB67615b88d9aaad175b567ed1a2a20aa($context, $indent, $value);
                $buffer .= $indent . '            </select>
';
                $buffer .= $indent . '        </span>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section0d52b32a6141e2a0a08221846e69769c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'workflowfilter, mod_assign';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'workflowfilter, mod_assign';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section88305569c34b99f418cae383f64a94fc(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <span class="row px-3 py-1">
            <label class="text-end w-25 p-2 m-0" for="filter-workflow-{{uniqid}}">
                {{#str}}workflowfilter, mod_assign{{/str}}
            </label>
            <select name="workflowfilter" class="custom-select w-50" id="filter-workflow-{{uniqid}}">
            {{#markingworkflowfilters}}
                <option value="{{key}}" {{#active}}selected="selected"{{/active}} > {{name}} </option>
            {{/markingworkflowfilters}}
            </select>
        </span>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <span class="row px-3 py-1">
';
                $buffer .= $indent . '            <label class="text-end w-25 p-2 m-0" for="filter-workflow-';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '                ';
                $value = $context->find('str');
                $buffer .= $this->section0d52b32a6141e2a0a08221846e69769c($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            </label>
';
                $buffer .= $indent . '            <select name="workflowfilter" class="custom-select w-50" id="filter-workflow-';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $value = $context->find('markingworkflowfilters');
                $buffer .= $this->sectionB67615b88d9aaad175b567ed1a2a20aa($context, $indent, $value);
                $buffer .= $indent . '            </select>
';
                $buffer .= $indent . '        </span>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
