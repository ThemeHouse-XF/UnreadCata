<?php

/**
 *
 * @see XenForo_Model_Alert
 */
class ThemeHouse_UnreadCat_Extend_XenForo_Model_Alert extends XFCP_ThemeHouse_UnreadCat_Extend_XenForo_Model_Alert
{

    /**
     *
     * @see XenForo_Model_Alert::getAlertsForUser()
     */
    public function getAlertsForUser($userId, $fetchMode, array $fetchOptions = array(), array $viewingUser = null)
    {
        $this->standardizeViewingUserReference($viewingUser);

        $categoryAlerts = array();

        $xenOptions = XenForo_Application::get('options');

        $categoryIds = array();
        if (!empty($xenOptions->th_unreadCategories_nodeIds)) {
            $categoryIds = $xenOptions->th_unreadCategories_nodeIds;

            foreach ($categoryIds as $nodeId) {
                $categoryAlerts[$nodeId] = array(
                    'alert_id' => 0,
                    'user_id' => 0,
                    'username' => '',
                    'gender' => '',
                    'avatar_date' => '',
                    'gravatar' => '',
                    'content_type' => 'unread_category_th',
                    'content_id' => $nodeId,
                    'action' => 'unread',
                    'event_date' => XenForo_Application::$time,
                    'view_date' => 0,
                    'message_count' => 0
                );
            }
        }

        $readCategories = $this->fetchAllKeyed(
            '
                SELECT node_id, category_read_date, message_count
                FROM xf_category_read_th
                WHERE user_id = ?
            ', 'node_id', $viewingUser['user_id']);

        if ($readCategories) {
            foreach ($readCategories as $nodeId => $readCategory) {
                if (in_array($nodeId, $categoryIds)) {
                    $categoryAlerts[$nodeId] = array(
                        'alert_id' => 0,
                        'user_id' => 0,
                        'username' => '',
                        'gender' => '',
                        'avatar_date' => '',
                        'gravatar' => '',
                        'content_type' => 'unread_category_th',
                        'content_id' => $nodeId,
                        'action' => 'unread',
                        'event_date' => XenForo_Application::$time,
                        'view_date' => $readCategory['category_read_date'],
                        'message_count' => $readCategory['message_count']
                    );
                }
            }
        }

        $categoryAlerts = $this->_getContentForAlerts($categoryAlerts, $userId, $viewingUser);

        foreach ($categoryAlerts as $alertId => $categoryAlert) {
            if (!empty($categoryAlert['content']['last_post_date'])) {
                $categoryAlerts[$alertId]['event_date'] = $categoryAlert['content']['last_post_date'];
                if ($categoryAlert['view_date'] < $categoryAlert['content']['last_post_date']) {
                    $categoryAlerts[$alertId]['view_date'] = 0;
                }
            }
        }

        $timeNow = XenForo_Application::$time;
        $options = XenForo_Application::get('options');

        switch ($fetchMode) {
            case self::FETCH_MODE_ALL:
                break;

            case self::FETCH_MODE_POPUP:
                foreach ($categoryAlerts as $alertId => $categoryAlert) {
                    if ($categoryAlert['event_date'] < ($timeNow - $options->alertsPopupExpiryHours * 3600)) {
                        unset($categoryAlerts[$alertId]);
                    }
                }
                break;

            case self::FETCH_MODE_RECENT:
            default:
                foreach ($categoryAlerts as $alertId => $categoryAlert) {
                    if ($categoryAlert['event_date'] < ($timeNow - $options->alertExpiryDays * 86400)) {
                        unset($categoryAlerts[$alertId]);
                    }
                }
                break;
        }

        $categoryAlerts = $this->_getViewableAlerts($categoryAlerts, $viewingUser);

        $unreadCategoryIds = array();
        foreach ($categoryAlerts as $categoryAlert) {
            if (empty($categoryAlert['view_date'])) {
                $unreadCategoryIds[] = $categoryAlert['content_id'];
            }
        }

        sort($unreadCategoryIds);
        $newUnreadCategoryIds = implode(',', $unreadCategoryIds);

        $oldUnreadCategoryIds = '';
        if (!empty($viewingUser['unread_category_ids_th'])) {
            $oldUnreadCategoryIds = $viewingUser['unread_category_ids_th'];
        }

        if ($newUnreadCategoryIds != $oldUnreadCategoryIds) {
            $this->_getDb()->query(
                '
                UPDATE xf_user_profile
                SET unread_category_ids_th = ?
                WHERE user_id = ?
            ',
                array(
                    $newUnreadCategoryIds,
                    $viewingUser['user_id']
                ));
        }

        $categoryAlerts = $this->prepareAlerts($categoryAlerts, $viewingUser);

        $alerts = parent::getAlertsForUser($userId, $fetchMode, $fetchOptions, $viewingUser);

        $alerts = array_merge($alerts['alerts'], $categoryAlerts);

        $eventDates = array();
        foreach ($alerts as $key => $row) {
            $eventDates[$key] = $row['event_date'];
        }
        array_multisort($eventDates, SORT_DESC, $alerts);

        return array(
            'alerts' => $alerts,
            'alertHandlers' => $this->_handlerCache
        );
    } /* END getAlertsForUser */
}