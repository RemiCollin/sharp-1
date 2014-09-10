<?php namespace Dvlpp\Sharp\Form\Fields;

use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Exceptions\MandatoryMethodNotFoundException;
use App;
use Form;
use Input;
use Lang;

/**
 * A multiple tags input.
 *
 * Class PivotTagsField
 * @package Dvlpp\Sharp\Form\Fields
 */
class PivotTagsField extends AbstractSharpField {

    /**
     * The actual HTML creation of the field.
     *
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryMethodNotFoundException
     */
    function make()
    {
        $this->_checkMandatoryAttributes(["repository"]);

        $reflistRepoName = $this->field->repository;

        if(class_exists($reflistRepoName) || interface_exists($reflistRepoName))
        {
            $reflistRepo = App::make($reflistRepoName);

            $create = $this->field->addable;
            if($create !== null)
            {
                $this->addData("addable", $create);
            }

            $this->addData("add_text", Lang::get('sharp::ui.form_pivotTagsField_addText'));

            if(!$this->instance && $this->isListItem)
            {
                // No instance and part of a list item : this field is meant to be in the template item.
                // In this case, we don't set the "sharp-tags" class which will trigger the JS code for
                // the selectize component creation
                $this->addClass("sharp-tags-template");
            }
            else
            {
                // Regular case
                $this->addClass("sharp-tags");
            }

            // Have to set multiple attribute in order to properly generate the field
            $this->attributes["multiple"] = "multiple";

            if(method_exists($reflistRepo, "formList"))
            {
                $values = $reflistRepo->formList($this->instance);

                // Initial value is tricky...
                $value = [];
                if($this->getOldValue())
                {
                    // Repopulate after validation error
                    $valuesToAdd = [];
                    foreach($this->getOldValue() as $val)
                    {
                        if(!is_numeric($val))
                        {
                            // Tag was created by user before. Have to tell selectize.js to add this non existent option in the list
                            $valuesToAdd[] = $val;
                        }
                        $value[] = $val;
                    }
                    $this->addData("to_add", implode(",", $valuesToAdd));
                }
                elseif($this->fieldValue)
                {
                    foreach($this->fieldValue as $val)
                    {
                        $value[] = $val->id;
                    }
                }

                // Field name has to be an array (books[] for example) to generate an array on data post
                return Form::select($this->fieldName . "[]", $values, $value, $this->attributes);
            }

            throw new MandatoryMethodNotFoundException("Method formList(askingInstance) not found in the [$reflistRepoName] class");
        }
        else
        {
            throw new MandatoryClassNotFoundException("Class [$reflistRepoName] not found");
        }
    }
} 