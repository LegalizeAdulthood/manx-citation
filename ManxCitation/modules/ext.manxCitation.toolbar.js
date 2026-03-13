// SPDX-License-Identifier: GPL-2.0-only

(function() {
    'use strict';
    
    function addManxCitationButton() {
        $('#wpTextbox1').on('wikiEditor-toolbar-doneInitialSections', function() {
            $('#wpTextbox1').wikiEditor('addToToolbar', {
                section: 'main',
                group: 'insert',
                tools: {
                    'manxcitation': {
                        label: mw.msg('manxcitation-button-label'),
                        type: 'button',
                        icon: 'reference',
                        action: {
                            type: 'callback',
                            execute: function(context) {
                                promptForUrl();
                            }
                        }
                    }
                }
            });
        });
    }
    
    function promptForUrl() {
        var url = prompt(mw.msg('manxcitation-prompt'));
        if (url && url.match(/^https?:\/\/manx-docs\.org\/details\.php\/[0-9]+,[0-9]+$/)) {
            fetchCitation(url);
        } else if (url) {
            mw.notify(mw.msg('manxcitation-error-invalid-url'), { type: 'error' });
        }
    }
    
    function fetchCitation(url) {
        new mw.Api().get({
            action: 'manxcitation',
            url: url,
            format: 'json'
        }).done(function(data) {
            if (data.manxcitation && data.manxcitation.citation) {
                $('#wpTextbox1').textSelection('replaceSelection', data.manxcitation.citation);
                mw.notify(mw.msg('manxcitation-success'), { type: 'success' });
            }
        }).fail(function(error) {
            mw.notify(mw.msg('manxcitation-error-fetch'), { type: 'error' });
            console.error('ManxCitation error:', error);
        });
    }
    
    // Initialize when WikiEditor is ready
    mw.loader.using(['jquery.wikiEditor'], function() {
        $(addManxCitationButton);
    });
})();
