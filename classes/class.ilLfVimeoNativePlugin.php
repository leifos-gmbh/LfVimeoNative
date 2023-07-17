<?php

/**
 * Test Page Component plugin
 * @author Alexander Killing <killing@leifos.de>
 */
class ilLfVimeoNativePlugin extends ilPageComponentPlugin
{
    public function getPluginName()
    {
        return "LfVimeoNative";
    }

    public function isValidParentType($a_parent_type)
    {
        return true;
    }

    public function onClone(&$a_properties, $a_plugin_version)
    {
    }

    public function onDelete($a_properties, $a_plugin_version)
    {
    }
}