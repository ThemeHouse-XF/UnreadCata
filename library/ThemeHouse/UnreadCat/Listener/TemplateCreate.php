<?php

class ThemeHouse_UnreadCat_Listener_TemplateCreate extends ThemeHouse_Listener_TemplateCreate
{

    protected function _getTemplates()
    {
        return array(
            'forum_list',
            'category_view'
        );
    } /* END _getTemplates */

    public static function templateCreate(&$templateName, array &$params, XenForo_Template_Abstract $template)
    {
        $templateCreate = new ThemeHouse_UnreadCat_Listener_TemplateCreate($templateName, $params, $template);
        list($templateName, $params) = $templateCreate->run();
    } /* END templateCreate */

    protected function _forumList()
    {
        if (XenForo_Application::$versionId > 1020000) {
            $addOns = XenForo_Application::get('addOns');
            $isFltInstalled = !empty($addOns['ThemeHouse_ForumListTabs']);
        } else {
            $isFltInstalled = $this->getAddOnById('ThemeHouse_ForumListTabs') ? true : false;
        }

        if ($isFltInstalled) {
            $this->_template->addRequiredExternal('js', 'js/themehouse/unreadcategories/full/forum_list_tabs.js');
        }
    } /* END _forumList */

    protected function _categoryView()
    {
        if (XenForo_Application::$versionId > 1020000) {
            $addOns = XenForo_Application::get('addOns');
            $isFltInstalled = !empty($addOns['ThemeHouse_ForumListTabs']);
        } else {
            $isFltInstalled = $this->getAddOnById('ThemeHouse_ForumListTabs') ? true : false;
        }

        if ($isFltInstalled) {
            $this->_template->addRequiredExternal('js', 'js/themehouse/unreadcategories/full/forum_list_tabs.js');
        }
    } /* END _categoryView */
}