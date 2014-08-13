<?php  namespace Dvlpp\Sharp\Form\Fields;

use Dvlpp\Sharp\Exceptions\MandatoryEntityAttributeNotFoundException;
use Form;
use Illuminate\Database\Eloquent\Model;
use Mustache_Engine;

/**
 * A simple display Label, with no posted value.
 *
 * Class LabelField
 * @package Dvlpp\Sharp\Form\Fields
 */
class LabelField {

    /**
     * @var
     */
    private $field;

    /**
     * @var
     */
    private $instance;

    /**
     * @param $field
     * @param $instance
     */
    function __construct($field, $instance)
    {
        $this->field = $field;
        $this->instance = $instance;
    }

    /**
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryEntityAttributeNotFoundException
     */
    function make()
    {
        if($this->field->format === null)
        {
            throw new MandatoryEntityAttributeNotFoundException("LabelField : Mandatory attribute format can't be found");
        }

        $baseEntity = $this->instance;
        if($baseEntity instanceof Model)
        {
            // Eloquent Model case: in order to have properties to work wirth Mustache, we
            // have to cheat a little, adding a MustacheModelHelper Decorator to force
            // Mustache to take properties even if method exists (relation case)
            $baseEntity = new MustacheModelHelper($baseEntity);
        }

        $val = $this->_format($baseEntity, $this->field->format);

        $attributes = $this->field->attributes || [];
        $attributes["class"] = "control-label";

        return Form::label("", $val, $attributes);
    }

    /**
     * @param $entity
     * @param $format
     * @return string
     */
    private function _format($entity, $format)
    {
        $m = new Mustache_Engine;
        return $m->render($format, $entity);
    }

}

/**
 * Eloquent Model decorator, which purpose is to invert Mustache
 * order of precedence between Methods and Properties
 * (source: https://github.com/bobthecow/mustache.php/issues/156)
 *
 * Class MustacheModelHelper
 * @package Dvlpp\Sharp\Form\Fields
 */
class MustacheModelHelper
{
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function __get($key)
    {
        if ($this->model->$key)
        {
            return $this->model->$key;
        }
        else if (method_exists($this->model, $key))
        {
            return $this->model->$key();
        }
    }

    public function __isset($key)
    {
        if (isset($this->model->$key))
        {
            return true;
        }
        else if (method_exists($this->model, $key))
        {
            return true;
        }
        return false;
    }
}