(function() {
    tinymce.create('tinymce.plugins.enjoyinstagram', {
        init: function(ed, url) {

            ed.addCommand('mceenjoyinstagram', function() {
                ed.windowManager.open({
// call content via admin-ajax, no need to know the full plugin path
                    file: ajaxurl + '?action=enjoyinstagram_tinymce',
                    width: 220 + ed.getLang('enjoyinstagram.delta_width', 0),
                    height: 210 + ed.getLang('enjoyinstagram.delta_height', 0),
                    inline: 1
                }, {
                    plugin_url: url // Plugin absolute URL
                });
            });

// Register example button
            ed.addButton('enjoyinstagram', {
                title: 'enjoyinstagramshortcodes',
                cmd: 'mceenjoyinstagram',
                image: url + '/icon_enjoyinstagram.png'
            });

// Add a node change handler, selects the button in the UI when a image is selected
            ed.onNodeChange.add(function(ed, cm, n) {
                cm.setActive('enjoyinstagram', n.nodeName == 'IMG');
            });
        },
        getInfo: function() {
            return {
                longname: 'Plugin to add Enjoy Instagram Button',
                author: 'Mediabeta Srl',
                authorurl: 'http://www.mediabeta.com/',
                infourl: 'http://www.mediabeta.com/',
                version: "1.0"
            };
        }
    });

// Register plugin
    tinymce.PluginManager.add('enjoyinstagram', tinymce.plugins.enjoyinstagram);
})();
