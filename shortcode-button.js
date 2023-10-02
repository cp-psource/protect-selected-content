(function() {
    tinymce.PluginManager.add('protect', function(editor, url) {
        editor.addButton('protect', {
            text: 'Protect Content',
            icon: false, // You can set an icon here if needed
            onclick: function() {
                editor.windowManager.open({
                    title: 'Insert Protect Shortcode',
                    body: [
                        {
                            type: 'textbox',
                            name: 'password',
                            label: 'Password'
                        }
                    ],
                    onsubmit: function(e) {
                        editor.insertContent('[protect password="' + e.data.password + '"]' + editor.selection.getContent() + '[/protect]');
                    }
                });
            }
        });
    });
})();