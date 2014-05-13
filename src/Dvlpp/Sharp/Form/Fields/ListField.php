<?php namespace Dvlpp\Sharp\Form\Fields;

use Dvlpp\Sharp\Form\Facades\SharpCmsField;
use Form;
use Input;


class ListField extends AbstractSharpField {

    function make()
    {
        // Manage data attributes
        $strAttr = "";
        if($this->field->addable) $strAttr .= 'data-addable="'.$this->field->addable.'"';
        if($this->field->removable) $strAttr .= ' data-removable="'.$this->field->removable.'"';
        if($this->field->sortable) $strAttr .= ' data-sortable="'.$this->field->sortable.'"';
        if($this->field->add_button_text) $strAttr .= ' data-add_button_text="'.e($this->field->add_button_text).'"';

        $str = '<ul class="sharp-list list-group" '.$strAttr.'>';

        $listkey = $this->key;
        if(Input::old($listkey))
        {
            // Form is re-displayed (validation errors): have to grab old values instead of DB
            foreach(Input::old($listkey) as $item)
            {
                $str .= $this->createItem((object)$item);
            }
        }
        else
        {
            foreach($this->instance->$listkey as $item)
            {
                $str .= $this->createItem($item);
            }
        }

        if($this->field->addable)
        {
            $str .= $this->createTemplate();
        }

        $str .= '</ul>';

        return $str;
    }

    private function createTemplate()
    {
        return $this->createItem(null);
    }

    private function createItem($item)
    {
        $isTemplate = ($item === null);

        $hiddenKey = $this->key."[".($isTemplate?"--N--":$item->id)."][id]";

        $strItem = '<li class="list-group-item sharp-list-item '.($isTemplate?"template":"").'"><div class="row">'
            . Form::hidden($hiddenKey, $isTemplate?"N":$item->id, ["class"=>"sharp-list-item-id"]);

        foreach($this->field->item as $key)
        {
            $itemField = $this->field->item->$key;

            $strField = '<div class="col-md-' . ($itemField->field_width ?: "12") . '">'
                . '<div class="form-group sharp-field sharp-field-' . $itemField->type . '"'
                . ($itemField->conditional_display ? ' data-conditional_display='.$itemField->conditional_display : '')
                .'>' . SharpCmsField::make($key, $itemField, $item, $this->key)
                . '</div></div>';

            $strItem .= $strField;
        }

        if($this->field->removable)
        {
            $strRemove = $this->field->remove_btn_text ?: 'Supprimer cet item';
            $strItem .= '<div class="col-md-12"><a class="sharp-list-remove btn btn-sm"><i class="fa fa-times"></i> '.$strRemove.'</a></div>';
        }

        $strItem .= '</div></li>';

        return $strItem;
    }
} 