<?php

class ThemeHouse_UnreadCat_Install_Controller extends ThemeHouse_Install
{

    protected $_resourceManagerUrl = 'http://xenforo.com/community/resources/unread-categories.3087/';

    protected function _getTables()
    {
        return array(
            'xf_category_read_th' => array(
                'category_read_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY', /* END 'category_read_id' */
                'user_id' => 'int(10) unsigned NOT NULL', /* END 'user_id' */
                'node_id' => 'int(10) unsigned NOT NULL', /* END 'node_id' */
                'category_read_date' => 'int(10) unsigned NOT NULL', /* END 'category_read_date' */
                'message_count' => 'int(10) unsigned NOT NULL', /* END 'message_count' */
            ), /* END 'xf_category_read_th' */
        );
    } /* END _getTables */

    protected function _getTableChanges()
    {
        return array(
            'xf_user_profile' => array(
                'unread_category_ids_th' => 'varbinary(255) NOT NULL DEFAULT \'\'', /* END 'unread_category_ids_th' */
            ), /* END 'xf_user_profile' */
        );
    } /* END _getTableChanges */

    protected function _getUniqueKeys()
    {
        return array(
            'xf_category_read_th' => array(
                'user_id_node_id' => array(
                    'user_id',
                    'node_id'
                ), /* END 'user_id_node_id' */
            ), /* END 'xf_category_read_th' */
        );
    } /* END _getUniqueKeys */

    protected function _getKeys()
    {
        return array(
            'xf_category_read_th' => array(
                'node_id' => array(
                    'node_id'
                ), /* END 'node_id' */
                'category_read_date' => array(
                    'category_read_date'
                ), /* END 'category_read_date' */
            ), /* END 'xf_category_read_th' */
        );
    } /* END _getKeys */

    protected function _getContentTypes()
    {
        return array(
            'unread_category_th' => array(
                'addon_id' => 'ThemeHouse_UnreadCat', /* END 'addon_id' */
                'fields' => array(
                    'alert_handler_class' => 'ThemeHouse_UnreadCat_AlertHandler_UnreadCategory', /* END 'alert_handler_class' */
                ), /* END 'fields' */
            ), /* END 'unread_category_th' */
        );
    } /* END _getContentTypes */


    protected function _postInstall()
    {
        $addOn = $this->getModelFromCache('XenForo_Model_AddOn')->getAddOnById('YoYo_');
        if ($addOn) {
            $db->query("
                INSERT INTO xf_category_read_th (category_read_id, user_id, node_id, category_read_date, message_count)
                    SELECT category_read_id, user_id, node_id, category_read_date, message_count
                        FROM xf_category_read_waindigo"); 
            $db->query("
                UPDATE xf_user_profile
                    SET unread_category_ids_th=unread_category_ids_waindigo");
        }
    }
}