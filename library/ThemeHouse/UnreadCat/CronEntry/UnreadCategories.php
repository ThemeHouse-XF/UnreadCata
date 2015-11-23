<?php

class ThemeHouse_UnreadCat_CronEntry_UnreadCategories
{

    public static function unreadCategories()
    {
        $nodeModel = XenForo_Model::create('XenForo_Model_Node');

        $nodeData = $nodeModel->getNodeDataForListDisplay(false, 0);

        $xenOptions = XenForo_Application::get('options');

        $categoryIds = array();
        if (!empty($xenOptions->th_unreadCategories_nodeIds)) {
            $categoryIds = $xenOptions->th_unreadCategories_nodeIds;
        }

        $nodes = array();
        foreach ($nodeData['nodesGrouped'] as $parentNodeId => $groupedNodes) {
            foreach ($groupedNodes as $nodeId => $node) {
                if ($node['node_type_id'] == 'Category' && !empty($node['last_post_date']) &&
                     in_array($nodeId, $categoryIds)) {
                    $nodes[$nodeId] = $node;
                }
            }
        }

        $nodeLastPostDates = XenForo_Application::arrayColumn($nodes, 'last_post_date', 'node_id');

        $db = XenForo_Application::getDb();

        $timeNow = XenForo_Application::$time;

        $whereClauses = array();
        foreach ($nodeLastPostDates as $nodeId => $lastPostDate) {
            if ($lastPostDate > ($timeNow - $xenOptions->alertsPopupExpiryHours * 3600)) {
                $whereClauses[] = '(node_id = ' . $db->quote($nodeId) . ' AND category_read_date >= ' .
                     $db->quote($lastPostDate) . ')';
            } else {
                unset($nodeLastPostDates[$nodeId]);
            }
        }

        $readCategories = array();
        if ($whereClauses) {
            $readCategories = $db->fetchPairs(
                '
                    SELECT user_id, GROUP_CONCAT(node_id SEPARATOR \',\') AS node_ids FROM xf_category_read_th
                    WHERE ' . implode(' OR ', $whereClauses) . '
                    GROUP BY user_id
                ');
        }

        $readCategoryCombinations = array();
        $userCombinationIds = array();
        foreach ($readCategories as $userId => $nodeIds) {
            if (!in_array($nodeIds, $readCategoryCombinations)) {
                $readCategoryCombinations[] = $nodeIds;
            }
            $userCombinationIds[array_search($nodeIds, $readCategoryCombinations)][] = $userId;
        }

        $allNodeIds = array_keys($nodeLastPostDates);
        $updates = array();
        foreach ($readCategoryCombinations as $combinationId => $nodeIds) {
            $readNodeIds = explode(',', $nodeIds);
            $unreadNodeIds = array_diff($allNodeIds, $readNodeIds);
            $userIds = $userCombinationIds[$combinationId];
            $updates[] = 'WHEN user_id IN (' . $db->quote($userIds) . ') THEN ' .
                 $db->quote(implode(',', $unreadNodeIds));
        }

        if ($updates) {
            $db->query(
                '
                UPDATE xf_user_profile SET unread_category_ids_th = CASE
                ' . implode(' ', $updates) . '
                ELSE ' . $db->quote(implode(',', $allNodeIds)) . '
                END
            ');
        } else {
            $db->query(
                '
                UPDATE xf_user_profile
                SET unread_category_ids_th = ' . $db->quote(implode(',', $allNodeIds)) . '
            ');
        }
    } /* END unreadCategories */
}