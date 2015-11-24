<?php
class AdminPageFramework_Form_View__Resource___Head extends AdminPageFramework_WPUtility {
    public function __construct($sHeadActionHook) {
        add_action($sHeadActionHook, array($this, '_replyToInsertRequiredInlineScripts'));
    }
    public function _replyToInsertRequiredInlineScripts() {
        if ($this->hasBeenCalled(__METHOD__)) {
            return;
        }
        echo "<script type='text/javascript' class='admin-page-framework-form-script-required-in-head'>" . '/* <![CDATA[ */ ' . $this->_getScripts_RequiredInHead() . ' /* ]]> */' . "</script>";
    }
    private function _getScripts_RequiredInHead() {
        return 'document.write( "<style class=\'admin-page-framework-js-embedded-inline-style\'>' . esc_js($this->_getInlineCSS()) . '</style>" );';
    }
    private function _getInlineCSS() {
        $_oLoadingCSS = new AdminPageFramework_Form_View___CSS_Loading;
        $_oLoadingCSS->add($this->_getScriptElementConcealerCSSRules());
        return $_oLoadingCSS->get();
    }
    private function _getScriptElementConcealerCSSRules() {
        return ".admin-page-framework-form-js-on { visibility: hidden; }";
    }
}