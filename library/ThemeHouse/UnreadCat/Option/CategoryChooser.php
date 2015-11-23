<?php

class ThemeHouse_UnreadCat_Option_CategoryChooser extends XenForo_Option_NodeChooser
{

    /**
     *
     * @see XenForo_Option_NodeChooser::renderSelect
     */
    public static function renderSelect(XenForo_View $view, $fieldPrefix, array $preparedOption, $canEdit)
    {
        $preparedOption['nodeFilter'] = 'Category';
        return self::_render('th_option_list_option_sel_unreadcategories', $view, $fieldPrefix, $preparedOption, $canEdit);
    } /* END renderSelect */

    /**
     * Fetches a list of node options.
     *
     * @param array $selectedForums
     * @param mixed Include root forum (specify a phrase to represent the root
     * forum)
     * @param mixed Filter the options to allow only the specified type to be
     * selectable
     *
     * @return array
     */
    public static function getNodeOptions($selectedForums, $includeRoot = false, $filter = false)
    {
        $options = parent::getNodeOptions($selectedForums, $includeRoot, $filter);

        foreach ($selectedForums as $selectedForum) {
            $options[$selectedForum]['selected'] = true;
        }

        return $options;
    } /* END getNodeOptions */

    /**
     * Renders the node chooser option.
     *
     * @param string Name of template to render
     * @param XenForo_View $view View object
     * @param string $fieldPrefix Prefix for the HTML form field name
     * @param array $preparedOption Prepared option info
     * @param boolean $canEdit True if an "edit" link should appear
     *
     * @return XenForo_Template_Abstract Template object
     */
    protected static function _render($templateName, XenForo_View $view, $fieldPrefix, array $preparedOption, $canEdit)
    {
        $filter = isset($preparedOption['nodeFilter']) ? $preparedOption['nodeFilter'] : false;

        $preparedOption['formatParams'] = self::getNodeOptions(
            $preparedOption['option_value'],
            false,
            $filter
        );

        return XenForo_ViewAdmin_Helper_Option::renderOptionTemplateInternal(
            $templateName, $view, $fieldPrefix, $preparedOption, $canEdit
        );
    } /* END _render */
}