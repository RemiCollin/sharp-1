<?php namespace Dvlpp\Sharp\Form\Fields;

use Dvlpp\Sharp\Form\Facades\SharpCmsField;
use Illuminate\Support\Collection;

/**
 * An ordered list of items containing fields.
 *
 * Class ListField
 * @package Dvlpp\Sharp\Form\Fields
 */
class ListField extends AbstractSharpField
{

    /**
     * The actual HTML creation of the field.
     *
     * @return string
     */
    function make()
    {
        // Manage data attributes
        $strAttr = "";
        if ($this->field->addable) {
            $strAttr .= 'data-addable="' . $this->field->addable . '"';
        }
        if ($this->field->removable) {
            $strAttr .= ' data-removable="' . $this->field->removable . '"';
        }
        if ($this->field->sortable) {
            $strAttr .= ' data-sortable="' . $this->field->sortable . '"';
        }
        if ($this->field->add_button_text) {
            $strAttr .= ' data-add_button_text="' . e($this->field->add_button_text) . '"';
        }

        // Add this hidden to send the list with nothing in case of 0 item.
        // It's useful to post the empty list and be able to delete all
        // potentially existing items.
        $str = '<input type="hidden" name="' . $this->key . '" value="">'
                . '<ul class="sharp-list list-group" ' . $strAttr . '>';

        $listkey = $this->key;
        $collection = $this->relation
            ? ($this->instance && $this->instance->{$this->relation} ? $this->instance->{$this->relation}->{$this->relationKey} : [])
            : $this->instance->$listkey;

        foreach ($collection as $item) {
            $str .= $this->createItem($item);
        }

        if ($this->field->addable) {
            $str .= $this->createTemplate();
        }

        return $str . '</ul>';
    }

    /**
     * @return string
     */
    protected function createTemplate()
    {
        return $this->createItem(null);
    }

    /**
     * @param $item
     * @return string
     */
    protected function createItem($item)
    {
        $itemIdAttribute = $this->field->item_id_attribute ?: "id";
        $itemId = null;
        $isTemplate = ($item === null);

        if (!$isTemplate) {
            if ($this->instance->__sharp_duplication) {
                // Duplication case: we change each existing item ID to make
                // them like new ones.
                $itemId = (!starts_with($item->$itemIdAttribute, "N_") ? "N_" : "") . $item->$itemIdAttribute;
                $item->__sharp_duplication = true;
            } else {
                $itemId = $item->$itemIdAttribute;
            }
        }

        $hiddenKey = $this->key . "[" . ($isTemplate ? "--N--" : $itemId) . "][$itemIdAttribute]";

        $strItem = '<li class="list-group-item sharp-list-item ' . ($isTemplate ? "template" : "") . '">'
            . '<div class="row">'
            . $this->formBuilder()->hidden($hiddenKey, $isTemplate ? "N" : $itemId, ["class" => "sharp-list-item-id"])
            . $this->createItemField($item)
            . '</div>';

        if ($this->field->sortable || $this->field->removable) {
            $strItem .= '<div class="row"><div class="col-md-12">';

            if ($this->field->sortable) {
                $strItem .= '<a class="sort-handle btn btn-sm"><i class="fa fa-sort"></i></a>';
            }

            if ($this->field->removable) {
                $strRemove = $this->field->remove_button_text ?: trans('sharp::ui.form_listField_deleteItem');
                $strItem .= '<a class="sharp-list-remove btn btn-sm"><i class="fa fa-times"></i> ' . $strRemove . '</a>';
            }

            $strItem .= '</div></div>';
        }

        return $strItem . '</li>';
    }

    protected function createItemField($item)
    {
        $strItem = "";

        foreach ($this->field->item as $key) {
            $itemField = $this->field->item->$key;

            $strField = '<div class="col-md-' . ($itemField->field_width ?: "12") . '">'
                . '<div class="form-group sharp-field sharp-field-' . $itemField->type . '"'
                . ($itemField->conditional_display ? ' data-conditional_display=' . $itemField->conditional_display : '') . '>'
                . SharpCmsField::make($key, $itemField, $item, $this->key)
                . '</div></div>';

            $strItem .= $strField;
        }

        return $strItem;
    }
}