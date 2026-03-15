<?php
// SPDX-License-Identifier: GPL-2.0-only

class ManxCitationHooks {

    /**
     * Load our ResourceLoader module on edit pages
     *
     * @param OutputPage $out
     * @param Skin $skin
     */
    public static function onBeforePageDisplay(OutputPage $out, Skin $skin) {
        $action = $out->getContext()->getRequest()->getVal('action', 'view');

        // Load on edit and submit pages
        if (in_array($action, ['edit', 'submit'])) {
            $out->addModules('ext.manxCitation.toolbar');
        }
    }
}
