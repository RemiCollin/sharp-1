<?php

namespace Dvlpp\Sharp\Commands;

use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturn;
use Dvlpp\Sharp\ListView\SharpEntitiesListParams;

/**
 * Class SharpEntitiesListCommand
 * @package Dvlpp\Sharp\Commands
 */
abstract class SharpEntitiesListCommand {

    use SharpCommandReturnTrait;

    /**
     * Execute the command.
     *
     * @param \Dvlpp\Sharp\ListView\SharpEntitiesListParams $entitiesListParams
     * @return SharpCommandReturn
     */
    abstract function execute(SharpEntitiesListParams $entitiesListParams);

} 