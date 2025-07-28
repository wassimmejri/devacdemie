<?php

class __Mustache_843c511ecabe835494a5615d543a7bb6 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="container-fluid">
';
        $buffer .= $indent . '<div data-region="grading-navigation" class="row">
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '<div data-region="assignment-info" class="col-md-4">
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '<a href="';
        $value = $this->resolveValue($context->findDot('config.wwwroot'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '/course/view.php?id=';
        $value = $this->resolveValue($context->find('courseid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" title="';
        $value = $this->resolveValue($context->find('coursename'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '">';
        $value = $this->resolveValue($context->find('coursename'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '</a><br/>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '<a href="';
        $value = $this->resolveValue($context->findDot('config.wwwroot'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '/mod/assign/view.php?id=';
        $value = $this->resolveValue($context->find('cmid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" title="';
        $value = $this->resolveValue($context->find('name'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '">';
        $value = $this->resolveValue($context->find('name'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '</a>
';
        $buffer .= $indent . '
';
        $value = $context->find('caneditsettings');
        $buffer .= $this->section2e7e71f84b9b54a116302a9346f7c56b($context, $indent, $value);
        $buffer .= $indent . '
';
        $buffer .= $indent . '<br/>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '<a href="';
        $value = $this->resolveValue($context->findDot('config.wwwroot'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '/mod/assign/view.php?id=';
        $value = $this->resolveValue($context->find('cmid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '&action=';
        $value = $this->resolveValue($context->find('actiongrading'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '">';
        $value = $this->resolveValue($context->find('viewgrading'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '</a>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '<div role="tooltip" id="tooltip-';
        $value = $this->resolveValue($context->find('uniqid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" class="accesshide">
';
        $value = $context->find('duedate');
        $buffer .= $this->section92ac5970ba3e3c4c74c16c64e9fe9c3f($context, $indent, $value);
        $buffer .= $indent . '
';
        $value = $context->find('cutoffdate');
        $buffer .= $this->section2740778e42f1e6f02e0e0aee1e27a745($context, $indent, $value);
        $buffer .= $indent . '
';
        $value = $context->find('duedate');
        $buffer .= $this->section27b26c9ca97731c6ac82fdf8c219a321($context, $indent, $value);
        $buffer .= $indent . '</div>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '</div>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '<div data-region="user-info" class="col-md-4" data-assignmentid="';
        $value = $this->resolveValue($context->find('assignmentid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" data-groupid="';
        $value = $this->resolveValue($context->find('groupid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '">
';
        if ($partial = $this->mustache->loadPartial('mod_assign/grading_navigation_user_info')) {
            $buffer .= $partial->renderInternal($context, $indent . '    ');
        }
        $buffer .= $indent . '</div>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '<div data-region="user-selector" class="col-md-4">
';
        $buffer .= $indent . '    <div class="alignment">
';
        if ($partial = $this->mustache->loadPartial('mod_assign/grading_navigation_user_selector')) {
            $buffer .= $partial->renderInternal($context, $indent . '        ');
        }
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</div>
';
        $buffer .= $indent . '</div>
';
        $buffer .= $indent . '</div>
';
        $value = $context->find('js');
        $buffer .= $this->section3c687441b39e1239e3ab18a7a855982d($context, $indent, $value);

        return $buffer;
    }

    private function section0bb349a181ed9d49e19d879da5efd4de(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'editsettings';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'editsettings';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionFbfbf734947d6abf9cff877b7b854cbe(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 't/edit, core';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 't/edit, core';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section2e7e71f84b9b54a116302a9346f7c56b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
<a href="{{config.wwwroot}}/course/modedit.php?update={{cmid}}&return=1" aria-label="{{#str}}editsettings{{/str}}" title="{{#str}}editsettings{{/str}}">
    {{#pix}}t/edit, core{{/pix}}
</a>
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '<a href="';
                $value = $this->resolveValue($context->findDot('config.wwwroot'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '/course/modedit.php?update=';
                $value = $this->resolveValue($context->find('cmid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '&return=1" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->section0bb349a181ed9d49e19d879da5efd4de($context, $indent, $value);
                $buffer .= '" title="';
                $value = $context->find('str');
                $buffer .= $this->section0bb349a181ed9d49e19d879da5efd4de($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '    ';
                $value = $context->find('pix');
                $buffer .= $this->sectionFbfbf734947d6abf9cff877b7b854cbe($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '</a>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA16bbc9349eec7195a7f2e7ff15cd30c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'duedatecolon, mod_assign, {{duedatestr}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . 'duedatecolon, mod_assign, ';
                $value = $this->resolveValue($context->find('duedatestr'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section92ac5970ba3e3c4c74c16c64e9fe9c3f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
{{#str}}duedatecolon, mod_assign, {{duedatestr}}{{/str}}
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('str');
                $buffer .= $this->sectionA16bbc9349eec7195a7f2e7ff15cd30c($context, $indent, $value);
                $buffer .= '
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section2740778e42f1e6f02e0e0aee1e27a745(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
<br>{{cutoffdatestr}}
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '<br>';
                $value = $this->resolveValue($context->find('cutoffdatestr'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section27b26c9ca97731c6ac82fdf8c219a321(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
<br>{{timeremainingstr}}
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '<br>';
                $value = $this->resolveValue($context->find('timeremainingstr'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section3c687441b39e1239e3ab18a7a855982d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
require([\'mod_assign/grading_navigation\', \'core/tooltip\'], function(GradingNavigation, ToolTip) {
    new GradingNavigation(\'[data-region="user-selector"]\');
    new ToolTip(\'[data-region="assignment-tooltip"]\');
});
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . 'require([\'mod_assign/grading_navigation\', \'core/tooltip\'], function(GradingNavigation, ToolTip) {
';
                $buffer .= $indent . '    new GradingNavigation(\'[data-region="user-selector"]\');
';
                $buffer .= $indent . '    new ToolTip(\'[data-region="assignment-tooltip"]\');
';
                $buffer .= $indent . '});
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
