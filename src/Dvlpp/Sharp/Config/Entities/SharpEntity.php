<?php namespace Dvlpp\Sharp\Config\Entities;


class SharpEntity extends HasProperties {

    protected $mandatoryProperties = ["label", "repository"];

    protected $structProperties = [
        "list_template" => 'Dvlpp\Sharp\Config\Entities\SharpEntityListTemplate',
        "form_fields" => 'Dvlpp\Sharp\Config\Entities\SharpEntityFormFields',
        "form_layout" => 'Dvlpp\Sharp\Config\Entities\SharpEntityFormLayout'
    ];

}

