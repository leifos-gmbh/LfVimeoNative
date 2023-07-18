# LfVimeoNative

Allows to insert native vimeo videos as page elements, instead of using the mediaelement.js library.

**Requirements**

- ILIAS: 6.x


**Installation**


```
> mkdir -p Customizing/global/plugins/Services/COPage/PageComponent
> cd Customizing/global/plugins/Services/COPage/PageComponent
> git clone https://github.com/leifos-gmbh/LfVimeoNative.git
```

Enter ILIAS Administration > Plugins and activate/configure the plugin.

** Vimeo URLs **

The plugin needs to create the required embed code including an iframe tag for the video with a src attribute like https://player.vimeo.com/video/123456 or https://player.vimeo.com/video/123456?h=b1fded9860. These URLs differ from the URLs pointing directly to the videos on vimeo. ILIAS tries to translate the following direct URL formats into the player URL formats:

- https://vimeo.com/<ID>
- https://vimeo.com/<ID>/<h>
- https://vimeo.com/channels/staffpicks/<ID>
