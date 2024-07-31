<?php

/**
 * @author            Alexander Killing <killing@leifos.de>
 * @ilCtrl_isCalledBy ilLfVimeoNativePluginGUI: ilPCPluggedGUI
 * @ilCtrl_isCalledBy ilLfVimeoNativePluginGUI: ilUIPluginRouterGUI
 */
class ilLfVimeoNativePluginGUI extends ilPageComponentPluginGUI
{
    protected ilLanguage $lng;
    protected ilCtrl $ctrl;
    protected ilGlobalTemplateInterface $tpl;

    public function __construct()
    {
        global $DIC;

        parent::__construct();

        $this->lng = $DIC->language();
        $this->ctrl = $DIC->ctrl();
        $this->tpl = $DIC['tpl'];
    }

    public function executeCommand() : void
    {
        $next_class = $this->ctrl->getNextClass();

        switch ($next_class) {
            default:
                // perform valid commands
                $cmd = $this->ctrl->getCmd();
                if (in_array($cmd, array("create", "save", "edit", "update", "cancel"))) {
                    $this->$cmd();
                }
                break;
        }
    }

    public function insert() : void
    {
        $form = $this->initForm(true);
        $this->tpl->setContent($form->getHTML());
    }

    public function create() : void
    {
        $form = $this->initForm(true);
        if ($this->saveForm($form, true)) {
            $this->tpl->setOnScreenMessage("success", $this->lng->txt("msg_obj_modified"), true);
            $this->returnToParent();
        }
        $form->setValuesByPost();
        $this->tpl->setContent($form->getHTML());
    }

    public function edit() : void
    {
        $form = $this->initForm();

        $this->tpl->setContent($form->getHTML());
    }

    public function update() : void
    {
        $form = $this->initForm(false);
        if ($this->saveForm($form, false)) {
            $this->tpl->setOnScreenMessage("success", $this->lng->txt("msg_obj_modified"), true);
            $this->returnToParent();
        }
        $form->setValuesByPost();
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * Init editing form
     */
    protected function initForm(bool $a_create = false) : ilPropertyFormGUI
    {
        $form = new ilPropertyFormGUI();

        // page value
        $page_value = new ilTextInputGUI($this->plugin->txt('vimeo_url'), 'url');
        $page_value->setInfo($this->plugin->txt('vimeo_url_info'));
        $page_value->setRequired(true);
        $form->addItem($page_value);

        // save and cancel commands
        if ($a_create) {
            $this->addCreationButton($form);
            $form->addCommandButton("cancel", $this->lng->txt("cancel"));
            $form->setTitle($this->plugin->txt('cmd_insert'));
        } else {
            $prop = $this->getProperties();
            $page_value->setValue($prop['url']);
            $form->addCommandButton("update", $this->lng->txt("save"));
            $form->addCommandButton("cancel", $this->lng->txt("cancel"));
            $form->setTitle($this->plugin->txt('edit_input_field'));
        }

        $form->setFormAction($this->ctrl->getFormAction($this));
        return $form;
    }

    protected function saveForm(ilPropertyFormGUI $form, bool $a_create) : bool
    {
        if ($form->checkInput()) {
            //$properties = $this->getProperties();

            // value saved in the page
            $properties['url'] = $form->getInput('url');

            if ($a_create) {
                return $this->createElement($properties);
            } else {
                return $this->updateElement($properties);
            }
        }

        return false;
    }

    public function cancel()
    {
        $this->returnToParent();
    }

    public function getElementHTML(string $a_mode, array $a_properties, string $a_plugin_version) : string
    {
        $orig_url = $a_properties["url"] ?? "";

        $is_vimeo = false;
        $is_youtube = false;

        // we need format https://player.vimeo.com/video/777755005 or
        // we need format https://player.vimeo.com/video/777755005?h=b1fded9860

        // handle format https://vimeo.com/<ID>
        if (substr($orig_url, 0, 18) === "https://vimeo.com/"){
            $id = substr($orig_url, 18);

            // handle format https://vimeo.com/<ID>/<h>
            if ($p = is_int(strpos($id, "/"))) {
                $parts = explode("/", $id);
                $id = $parts[0] . "?h=" . $parts[1];
                $url = "https://player.vimeo.com/video/" . $id;
                $is_vimeo = true;
            }

            if (is_numeric($id)) {
                $url = "https://player.vimeo.com/video/" . $id;
                $is_vimeo = true;
            }
        }

        // handle format https://vimeo.com/channels/staffpicks/762208496
        if (substr($orig_url, 0, 27) === "https://vimeo.com/channels/") {
            $last = strrpos($orig_url, "/");
            $id = substr($orig_url, $last + 1);
            if (is_numeric($id)) {
                $url = "https://player.vimeo.com/video/" . $id;
                $is_vimeo = true;
            }
        }

        if (ilExternalMediaAnalyzer::isYouTube($orig_url)) {
            $par = ilExternalMediaAnalyzer::extractYouTubeParameters($orig_url);
            if (($par["v"] ?? "") !== "") {
                $url = "https://www.youtube.com/embed/" . $par["v"];
                $is_youtube = true;
            }
        }


        if ($is_vimeo) {
            $html = <<<EOT
<div style="padding:52.73% 0 0 0;position:relative;"><iframe src="$url" style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe></div><script src="https://player.vimeo.com/api/player.js"></script>
EOT;
        }

        if ($is_youtube) {
            $html = <<<EOT
<iframe style="width:100%; aspect-ratio: 16/9" src="$url"  frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
EOT;
        }


        if ($a_mode === "edit") {
            $html = "<div style='height:20px;'></div>" . $html . "<div style='height:20px;'></div>";
        }

        return $html;
    }
}