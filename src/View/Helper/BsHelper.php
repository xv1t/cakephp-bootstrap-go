<?php

namespace BootstrapGo\View\Helper;

use Cake\View\Helper;

/**
 * Helper class for Cajas plugin
 * @link https://github.com/xv1t/cakephp-cajas Official web page
 */
class BsHelper extends Helper
{
    public $helpers = ['Html', 'Form'];
    
    public function test()
    {
        return 'BootstrapGo\View\Helper';
    }
}