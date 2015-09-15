<?php namespace Dvlpp\Sharp\Form\Fields;

use Collective\Html\FormBuilder;
use Dvlpp\Sharp\Config\Entities\SharpEntityFormField;
use Dvlpp\Sharp\Exceptions\MandatoryEntityAttributeNotFoundException;
use stdClass;

/**
 * This this all Sharp's fields superclass.
 *
 * Class AbstractSharpField
 * @package Dvlpp\Sharp\Form\Fields
 */
abstract class AbstractSharpField
{
    /**
     * @var string
     */
    protected $key;
    /**
     * @var SharpEntityFormField
     */
    protected $field;
    /**
     * @var string
     */
    protected $fieldName;
    /**
     * @var array
     */
    protected $attributes;
    /**
     * @var
     */
    protected $instance;
    /**
     * @var string
     */
    protected $fieldValue;
    /**
     * @var string
     */
    protected $listKey;
    /**
     * @var bool
     */
    protected $isListItem = false;
    /**
     * @var string
     */
    protected $relation;
    /**
     * @var string
     */
    protected $relationKey;

    /**
     * FormBuilder
     */
    private $formBuilder;

    /**
     * Construct the field.
     *
     * @param $key
     * @param $listKey
     * @param SharpEntityFormField $field
     * @param $attributes
     * @param $instance
     */
    function __construct($key, $listKey, $field, $attributes, $instance)
    {
        $this->key = $key;
        $this->field = $field;
        $this->attributes = $attributes;
        $this->instance = $instance;

        if ($listKey) {
            $this->listKey = $listKey;
            $this->isListItem = true;
        }

        $this->fieldName = $this->buildFieldName();

        $this->relation = null;
        $this->relationKey = null;

        if (strpos($key, "~")) {
            // If there's a "~" in the field $key, this means we are in a single relation case
            // (One-To-One or Belongs To). The ~ separate the relation name and the value.
            // For instance : boss~name indicate that the instance as a single "boss" relation,
            // which has a "name" attribute.
            list($this->relation, $this->relationKey) = explode("~", $key);

            $this->fieldValue = $instance && !$instance instanceof stdClass && $instance->{$this->relation}
                ? $instance->{$this->relation}->{$this->relationKey}
                : null;

        } else {
            // Value is instance->key, except for null key: in this case, value IS the instance
            $this->fieldValue = $key ? ($instance ? $instance->$key : null) : $instance;
        }
    }

    /**
     * Add a class name to the class attribute.
     *
     * @param $className
     * @param bool $handleTemplateCase
     */
    protected function addClass($className, $handleTemplateCase=false)
    {
        if($handleTemplateCase && !$this->instance && $this->isListItem) {
            // No instance and part of a list item : this field is meant to be in the template item.
            // In this case, we don't set the "sharp-date" class which will trigger the JS code for
            // the date component creation
            $this->addClass("$className-template");

        } else {
            $this->attributes["class"] = $className . (array_key_exists("class",
                    $this->attributes) ? " " . $this->attributes["class"] : "");
        }
    }

    /**
     * Add a data-XXX attribute
     *
     * @param $name
     * @param $data
     */
    protected function addData($name, $data)
    {
        $this->attributes["data-$name"] = $data;
    }

    /**
     * Check for missing mandatory attributes.
     *
     * @param array $attributes
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryEntityAttributeNotFoundException
     */
    protected function _checkMandatoryAttributes(Array $attributes)
    {
        foreach ($attributes as $attr) {
            if ($this->field->$attr === null) {
                throw new MandatoryEntityAttributeNotFoundException("Attribute [$attr] can't be found (Field: " . $this->key . ")");
            }
        }
    }

    protected function formBuilder()
    {
        if(!$this->formBuilder) {
            $this->formBuilder = app(FormBuilder::class);
        }

        return $this->formBuilder;
    }

    /**
     * The actual HTML creation of the field.
     *
     * @return mixed
     */
    abstract function make();

    /**
     * Build the field name, depending on it's a list item or not, and if it's for
     * a duplication or not.
     * TODO supprimer l'accès en dur à l'attribut "id" de l'item dans le cas d'une liste
     *
     * @return string
     */
    private function buildFieldName()
    {
        if ($this->isListItem) {
            // It's a list item
            $str = $this->listKey . "[";

            if ($this->instance) {
                if (isset($this->instance->__sharp_duplication)
                    && $this->instance->__sharp_duplication
                    && !starts_with($this->instance->id, "N_")
                ) {
                    // It's for a duplicated instance (creation with data): we have
                    // to set the field ID to new
                    $str .= "N_" . $this->instance->id;
                } else {
                    $str .= $this->instance->id;
                }
            } else {
                $str .= "--N--";
            }

            $str .= "]" . ($this->key ? "[" . $this->key . "]" : "");

            return $str;

        } else {
            return $this->key;
        }
    }
}