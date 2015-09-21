<?php namespace Dvlpp\Sharp\Commands;

use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturn;

/**
 * Interface SharpEntityCommand
 * @package Dvlpp\Sharp\Commands
 */
abstract class SharpEntityCommand {

    use CommandReturnTrait;

    /**
     * Execute the entity command.
     *
     * @param $instanceId
     * @param array $params
     * @return SharpCommandReturn
     */
    abstract function execute($instanceId, array $params=[]);

} 