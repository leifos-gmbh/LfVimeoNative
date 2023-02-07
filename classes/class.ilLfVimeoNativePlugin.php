<?php

/**
 * Test Page Component plugin
 * @author Alexander Killing <killing@leifos.de>
 */
class ilLfVimeoNativePlugin extends ilPageComponentPlugin
{
    public function getPluginName() : string
    {
        return "LfVimeoNative";
    }

    public function isValidParentType(string $a_parent_type) : bool
    {
        return true;
    }

    public function onClone(array &$a_properties, string $a_plugin_version) : void
    {
    }

    public function onDelete(array $a_properties, string $a_plugin_version, bool $move_operation = false) : void
    {
    }
}