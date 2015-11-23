<?php

/**
 *
 * @see XenForo_Model_User
 */
class ThemeHouse_UnreadCat_Extend_XenForo_Model_User extends XFCP_ThemeHouse_UnreadCat_Extend_XenForo_Model_User
{

    /**
     *
     * @see XenForo_Model_User::getVisitingUserById()
     */
    public function getVisitingUserById($userId)
    {
        $user = parent::getVisitingUserById($userId);

        if (!empty($user['unread_category_ids_th'])) {
            $unreadCategoryIds = explode(',', $user['unread_category_ids_th']);
            $user['alerts_unread'] += count($unreadCategoryIds);
        }

        return $user;
    } /* END getVisitingUserById */
}