<?php

class ThemeHouse_UnreadCat_Listener_LoadClass extends ThemeHouse_Listener_LoadClass
{

    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_UnreadCat' => array(
                'model' => array(
                    'XenForo_Model_Alert',
                    'XenForo_Model_User',
                    'XenForo_Model_Category'
                ), /* END 'model' */
                'controller' => array(
                    'XenForo_ControllerPublic_Category'
                ), /* END 'controller' */
                'datawriter' => array(
                    'XenForo_DataWriter_DiscussionMessage_Post'
                ), /* END 'datawriter' */
            ), /* END 'ThemeHouse_UnreadCat' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassModel($class, array &$extend)
    {
        $loadClassModel = new ThemeHouse_UnreadCat_Listener_LoadClass($class, $extend, 'model');
        $extend = $loadClassModel->run();
    } /* END loadClassModel */

    public static function loadClassController($class, array &$extend)
    {
        $loadClassController = new ThemeHouse_UnreadCat_Listener_LoadClass($class, $extend, 'controller');
        $extend = $loadClassController->run();
    } /* END loadClassController */

    public static function loadClassDataWriter($class, array &$extend)
    {
        $loadClassDataWriter = new ThemeHouse_UnreadCat_Listener_LoadClass($class, $extend, 'datawriter');
        $extend = $loadClassDataWriter->run();
    } /* END loadClassDataWriter */
}