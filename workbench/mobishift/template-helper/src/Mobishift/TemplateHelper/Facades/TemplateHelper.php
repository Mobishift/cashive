<?php namespace Mobishift\TemplateHelper\Facades;

use Illuminate\Support\Facades\Facade;

class TemplateHelper extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'helper'; }

}