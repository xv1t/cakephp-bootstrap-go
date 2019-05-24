<?php

namespace BootstrapGo\View\Helper;

use Cake\View\Helper;

/**
 * Helper class for Bootstrap4 plugin
 * 
 * @link https://github.com/xv1t/cakephp-bootstrap-go
 * 
 * @property \Cake\View\Helper\HtmlHelper $Html Description
 * @property \Cake\View\Helper\FormHelper $Form Description
 */
class BsHelper extends Helper
{
    public $helpers = ['Html', 'Form'];
    
    public function defaultPrivateOption(&$options, &$_options, $key, $defaultValue){
        if (array_key_exists($key, $options)){
            $_options[$key] = $options[$key];
            unset($options[$key]);
        } else {
            $_options[$key] = $defaultValue;
        }
    }

    public function defaultPrivateOptions(&$options, &$_options, $array)
    {
        foreach ($array as $key => $defaultValue) {
            $this->defaultPrivateOption($options, $_options, $key, $defaultValue);
        }
    }

    public function defaultOptions(&$options, $array)
    {
        foreach ($array as $key => $default_value) {
            if (! array_key_exists($key, $options) ) {
                $options[$key] = $default_value;
            }
        }
    }

    public function _size($baseName, &$options, $_options, $default = '') 
    {
        if (!empty($_options['size'])) {
                $options = $this->Html->addClass(
                    $options, 
                    $baseName . '-' . $_options['size']
                );
        }
    }

    public function _style($baseName, &$options = [], $_options = [], $default = '')
    {
        if (!empty($_options['style'])) {

                $options = $this->Html->addClass(
                    $options, 
                    $baseName . '-' . $_options['style']
                );
        }
    }

    /**
     * Fontawesome icon
     */
    public function fa($icon, $options = [])
    {
        $tagName = $this->_extractOptionsKey($options, 'tagName', 'i');
        $options = $this->Html->addClass($options, $icon);

        return $this->Html->tag($tagName, '', $options);
    }

    /**
     * Bootstrap Spacing
     * @link https://getbootstrap.com/docs/4.3/utilities/spacing/
     * 
     * @param $options Array
     * @return array
     */
    public function _spacing(&$options)
    {
        $properties = [
            'm' => 'margin',
            'p' => 'padding'
        ];

        $sides = [
            't' => 'top',
            'b' => 'bottom',
            'l' => 'left',
            'r' => 'right',
            'x' => ['left', 'right'],
            'y' => ['top', 'bottom']            
        ];

        foreach ($properties as $property => $tmp1) {
            foreach ($sides as $side => $tmp2) {
                $word = $property . $side;
                if (array_key_exists($word, ($options))) {
                    $options = $this->Html->addClass(
                        $options,
                        $word . '-' . $options[$word]
                    );
                    unset($options[$word]);
                }
            }

            //find once 'm' or 'p' keys
            if (array_key_exists($property, $options)) {
                $options = $this->Html->addClass(
                    $options,
                    $property . '-' . $options[$property]
                );
                unset($options[$property]);
            }
        }
        return $options;
    }

    /**
     * Add a active class 
     * 
     * @link https://getbootstrap.com/docs/4.3/components/buttons/#active-state      
     */
    function _active(&$options, $_options) 
    {
        if (!empty($_options['active'])) {
            $options = $this->Html->addClass($options, 'active');
        }
    }

    /**
     * Add a block class
     */
    function _block($baseName, &$options, $_options) 
    {
        if (!empty($_options['block'])) {
            $options = $this->Html->addClass($options, "$baseName-block");
        }
    }

    public function wrap($content, $wrapTag = 'div', $wrapOptions = [])
    {        
        return $this->Html->tag(
            $wrapTag,
            $content,
            $wrapOptions
        );
    }

    public function card($options) 
    {
        
    }
    
    /**
     * Use Bootstrap’s custom button
     *      
     * @param string|array $label|$options 
     * @param array options
     * @return  string HTML code
     * 
     * @link https://getbootstrap.com/docs/4.3/components/buttons/     
     */
    public function button($label = '', $options = [])
    {

        if (is_string($label)) {
            $options['content'] = $label;
        }

        if (is_array($label)) {
            $options = $label;
        }

        $options = $this->Html->addClass($options, 'btn');

        $_options = [];

        $this->defaultPrivateOptions(
            $options, 
            $_options, 
            [
                '_type' => 'button',
                'tagName' => 'button',
                'style' => 'secondary',
                'outline' => false,
                'size' => null,
                'content' => '',
                'active' => false,
                //'disabled' => false,
                'block' => false,
                'icon' => false,
                'iconRight' => false
            ]
        );

        $this->_size('btn', $options, $_options);

        /* @link https://getbootstrap.com/docs/4.3/components/buttons/#outline-buttons */
        $outline = $_options['outline'] ? '-outline' : null;

        $this->_style('btn' . $outline, $options, $_options);
        $this->_spacing($options);
        $this->_active($options, $_options);
        $this->_block('btn', $options, $_options);

        $iconRight = $iconLeft = '';

        if ($_options['icon']) {
            $iconContent = $this->fa($_options['icon']);
            if ($_options['iconRight']) {
                $iconRight = ' ' . $iconContent;
            } else {
                $iconLeft = $iconContent . ' ';
            }
        }

        return $this->Html->tag(
            $_options['tagName'], 
            $iconLeft . $_options['content'] . $iconRight,  
            $options
        );
    }
    /**
     * 
     * @link https://getbootstrap.com/docs/4.3/components/button-group/#button-toolbar
     */
    function button_toolbar($button_groups = [], $options = []) 
    {
        $options = $this->Html->addClass($options, 'btn-toolbar');
        $content = '';

        foreach ($button_groups as $button_group) {
            if (is_string($button_group)) {
                $content .= $button_group;
            } else {
                $content .= $this->button_group($button_group);
            }
        }

        return $this->Html->tag(
            'div',
            $content,
            $options
        );
    }

    public function navtabs($options)
    {
        $contentDirty = $this->_extractOptionsKey($options, 'content', []);
        $tabListOptions = $this->_extractOptionsKey($options, 'tablist', []);
        $tabContentOptions = $this->_extractOptionsKey($options, 'tabcontent', []);
        $activeTab = $this->_extractOptionsKey($options, 'active', 0);
        $tabs = [];

        $html = '';


        if (!$activeTab) {
            $activeTab = 0;
        }

        //transorm simple to full options objects
        $index = 0;
        foreach ($contentDirty as $_one => $_two) {
            $tab = [
                'tab' => [],
                'pane' => []
            ];

            if (is_string($_one)) {
                $tab['tab']['content'] = $_one;

                if (is_array($_two)) {
                    $tab['pane'] = $_two;
                }
            }

            if (is_string($_two)) {
                $tab['pane']['content'] = $_two;
            }

            if (\is_numeric($_one)) {
                $tab = $_two;
            }

            if (is_string($tab['tab'])) {
                $tab['tab'] = [
                    'content' => $tab['tab']
                ];
            }

            if (is_string($tab['pane'])) {
                $tab['pane'] = [
                    'content' => $tab['pane']
                ];
            }

            $id = uniqid();

            if (empty($tab['tab']['id'])) {
                $tab['tab']['id'] = "tab-$id";
            }

            if (empty($tab['pane']['id'])) {
                $tab['pane']['id'] = "pane-$id";
            }
           
            $tab['active'] = false;
            if (is_numeric($activeTab) && $activeTab === $index) {
                $tab['active'] = true;
            }

            if (is_string($activeTab) 
                && ($activeTab == $tab['tab']['id'] 
                || $activeTab == $tab['tab']['content'] )
            ) {
                $tab['active'] = true;
            }

            $tabs[] = $tab;
            $index++;
        }

        $tabsContent = $panesContent = '';
        foreach ($tabs as $tabIndex => $tab) {
            $a_content = $this->_extractOptionsKey($tab['tab'], 'content', '');
            $a_options = $tab['tab'];            
            $a_options = $this->Html->addClass($a_options, 'nav-item nav-link');
            $a_options += [
                'data-toggle' => 'tab',
                'role' => 'tab',
                'aria-controls' => $tab['pane']['id'],
                'aria-selected' => $tab['active'] ? 'true' : 'false',
                'href' => '#' . $tab['pane']['id'],
                ];
            if ($tab['active']) {
                $a_options = $this->Html->addClass($a_options, 'active');
            }
            
            $tabsContent .= $this->Html->tag('a', $a_content, $a_options);

            $pane_content = $this->_extractOptionsKey($tab['pane'], 'content', '');

            $pane_options = $tab['pane'];
            $pane_options = $this->Html->addClass($pane_options, 'tab-pane fade');
            $pane_options += [
                'role' => 'tabpanel',
                'aria-labelledby' => $tab['tab']['id'],                
            ];

            if ($tab['active']) {
                $pane_options = $this->Html->addClass($pane_options, 'show active');
            }

            $panesContent .=  $this->Html->tag(
                'div', 
                $pane_content, 
                $pane_options
            );
        }

        $tabListOptions['role'] = 'tablist';
        $tabListOptions = $this->Html->addClass($tabListOptions, 'nav nav-tabs');

        $this->_spacing($tabListOptions);
        $this->_spacing($tabContentOptions);
        $this->_spacing($options);
        $tabContentOptions = $this->Html->addClass(
            $tabContentOptions, 
            'tab-content'
        );

        return $this->Html->tag(
            'div',
            join([
                $this->Html->tag('nav', $tabsContent, $tabListOptions),
                $this->Html->tag('div', $panesContent, $tabContentOptions),
            ]),
            $options
        );
        
    }

    
    
    /**
     * 
     * @link https://api.cakephp.org/3.7/class-Cake.View.Helper.FormHelper.html#%24_defaultConfig
     * 
     */
    public function form($entity, $options)
    {

    }

    public function loremIpsum($count = 1) {
        $lorem = [
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
            'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.',
            'Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?'
        ];
        $out = '';

        for ($i = 0; $i < $count; $i++) {
            $out .= $lorem [array_rand($lorem)] . '<br>';
        }
        return $out;
    }

    /**
     * Button group
     * 
     * @link https://getbootstrap.com/docs/4.3/components/button-group/
     */
    public function button_group($buttons = [], $options = [])
    {
        if (array_key_exists('buttons', $buttons)) {
            $options = $buttons;
            $buttons = $this->_extractOptionsKey($options, 'buttons', []);
        }

        $_options = [];

        $this->defaultPrivateOptions(
            $options, 
            $_options, 
            [
                'vertical' => false,
                'size' => null
            ]
        );

        $this->_size('btn-group', $options, $_options);
        $this->_spacing($options);

        if ($_options['vertical']) {
            $options = $this->Html->addClass($options, 'btn-group-vertical');
        } else {
            $options = $this->Html->addClass($options, 'btn-group');
        }

        $content = '';

        foreach ($buttons as $button) {
            if (is_string($button)) {
                $content .= $button;
            };
            if (is_array($button)) {
                $content .= $this->button($button);
            }
        }

        $options['role'] = 'group';

        return $this->Html->tag('div', $content, $options);
    }

    public function _is_element($options)
    {
        return 
            is_array($options) 
            && array_key_exists('_type', $options)
            && array_key_exists('options', $options);
    }

    public function _has_elements($options)
    {
        if ($this->_is_element($options))
            return false;

        return is_array($options) && array_key_exists('elements', $options);
    }

    public function div($content = '', $options = [])
    {

        if (is_array($content)) {
            $options = $content;
            $content = '';
        }

        if (array_key_exists('content', $options)) {
            $content = $options['content'];
            unset($options['content']);
        }

        if ($this->_has_elements($options) ) {
            $content = $this->render($options);
        }

        return $this->Html->tag('div', $content, $options);
    }

    public function _extractOptionsKey(&$options, $keyName, $defaultValue = null)
    {
        if (array_key_exists($keyName, $options)) {
            $value = $options[$keyName];
            unset($options[$keyName]);
            return $value;
        }

        return $defaultValue;
    }

    public function tag($tagName, $options = []) 
    {
        if (is_array($tagName)) {
            $options = $tagName;
        }

        $content = $this->_extractOptionsKey($options, 'content', '');

        $tagName = $this->_extractOptionsKey($options, 'tagName', 'div');
        return $this->Html->tag($tagName, $content, $options);
    }


    /**
     * Extract aspect keys from options
     */
    public function _extractAspectOptions(&$options)
    {
        $aspectOptions = [];
        foreach (['sm', 'md', 'lg', 'xl'] as $key) {
            if (array_key_exists($key, $options) ) {
                $aspectOptions[$key] 
                    = $this->_extractOptionsKey($options, $key, false);
                unset($options[$key]);
            }
        }
        return $aspectOptions;
    }

    /**
     * Calculate class from aspect options
     */
    public function _aspect($options, $globalOptions)
    {
        $className = '';
        foreach (['sm', 'md', 'lg', 'xl'] as $key) {
            $aspect = null;
            if (array_key_exists($key, $options)  ) {
                $aspect = $options[$key];
            } else if (array_key_exists($key, $globalOptions) ) {
                $aspect = $globalOptions[$key];
            }

            if ($aspect === true) {
                $className .= "col-$key";
            } else if ($aspect) {
                $className .= "col-$key-" . $aspect;
            }
        }
        return $className;
    }

    /**
     * Check is aspect is present ib the options
     * 
     */
    public function _is_aspect($options)
    {
        foreach (['sm', 'md', 'lg', 'xl'] as $key) {
            if (!empty($options[$key])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Grid row
     * 
     * @link https://getbootstrap.com/docs/4.3/layout/grid/
     * 
     */
    public function row($options)
    {
        $rowHtml = '';
        $content = $this->_extractOptionsKey($options, 'content', []);
        $rowAspectOptions = $this->_extractAspectOptions($options);
        $col = $this->_extractOptionsKey($options, 'col', false); 

        foreach ($content as $item) {

            if ($item === false || $item === null) {
                continue;
            }

            $item_options = [];
            $item_content = '';

            if (is_string($item)) {
                $item_content = $item;
            }

            if (is_array($item)) {
                $item_options = $item;
                $item_content = $this->_extractOptionsKey($item_options, 'content', '');
            }

            $w = $this->_extractOptionsKey($item_options, 'w', false);
            
            $item_col = $this->_extractOptionsKey($item_options, 'col', false) ?: $col;
            $itemAspectOptions = $this->_extractAspectOptions($item_options);

            if (!$this->_is_aspect($itemAspectOptions) && !$this->_is_aspect($rowAspectOptions) && !$col) {
                $item_col = true;
            }

            if ($item_col && !$w) {
                if ($item_col === true) {
                     $item_options = $this->addClass($item_options, 'col');

                } else {
                    $item_options = $this->addClass($item_options, 'col-' . $item_col);

                }               
            }

            if ($w) {
                $item_options = $this->addClass($item_options, 'w-' . $w);
            } else {
                $item_options = $this->Html->addClass(
                    $item_options, 
                    $this->_aspect($itemAspectOptions, $rowAspectOptions)
                );
            }

                        $rowHtml .= $this->Html->tag('div', $item_content, $item_options);
        }

        $this->_spacing($options);

        $options = $this->Html->addClass($options, 'row');

        return $this->Html->tag('div', $rowHtml, $options);

    }

    /**
     * Return full bootstrap UI HTML from
     * array of elements
     */
    public function render($options)
    {
        $content = '';

        if ($this->_has_elements($options)) {
            
            foreach ($options['elements'] as $el) {
                $content .= $this->render($el);
            }
        } else {
            if ($this->_is_element($options)) {
                $method = $options['_type'];
                
                if (method_exists($this, $method)) {
                    $content .= $this->$method($options['options']);
                }
            } else if (is_string($options)) {
                $content .= $options;
            }
        }
        return $content;
    }
    
    public function test()
    {
        return 'BootstrapGo\View\Helper';
    }
}