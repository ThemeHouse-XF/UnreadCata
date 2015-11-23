<?php

/**
 *
 * @see XenForo_Model_Category
 */
class ThemeHouse_UnreadCat_Extend_XenForo_Model_Category extends XFCP_ThemeHouse_UnreadCat_Extend_XenForo_Model_Category
{

    /**
     * Marks the specified category as read up to a specific time.
     * Category must have the
     * category_read_date key.
     *
     * @param array $category Category info
     * @param integer $readDate Timestamp to mark as read until
     * @param integer $messageCount Message count at read date
     * @param array|null $viewingUser
     *
     * @return boolean True if marked as read
     */
    public function markCategoryRead(array $category, $readDate, $messageCount, array $viewingUser = null)
    {
        $this->standardizeViewingUserReference($viewingUser);

        $userId = $viewingUser['user_id'];
        if (!$userId) {
            return false;
        }

        if (!array_key_exists('category_read_date', $category)) {
            $category['category_read_date'] = $this->getUserCategoryReadDate($userId, $category['node_id']);
        }

        if ($readDate <= $category['category_read_date']) {
            return false;
        }

        $this->_getDb()->query(
            '
			INSERT INTO xf_category_read_th
				(user_id, node_id, category_read_date, message_count)
			VALUES
				(?, ?, ?, ?)
			ON DUPLICATE KEY UPDATE category_read_date = VALUES(category_read_date),
                message_count = VALUES(message_count)
		',
            array(
                $userId,
                $category['node_id'],
                $readDate,
                $messageCount
            ));

        $unreadCategoryIds = array();
        if (!empty($viewingUser['unread_category_ids_th'])) {
            $unreadCategoryIds = explode(',', $viewingUser['unread_category_ids_th']);
        }

        $key = array_search($category['node_id'], $unreadCategoryIds);
        if ($key !== false) {
            unset($unreadCategoryIds[$key]);
            $this->_getDb()->query(
                '
                    UPDATE xf_user_profile
                    SET unread_category_ids_th = ?
                    WHERE user_id = ?
                ',
                array(
                    implode(',', $unreadCategoryIds),
                    $viewingUser['user_id']
                ));
        }

        return true;
    } /* END markCategoryRead */

    /**
     * Get the time when a user has marked the given category as read.
     *
     * @param integer $userId
     * @param integer $nodeId
     *
     * @return integer null if guest; timestamp otherwise
     */
    public function getUserCategoryReadDate($userId, $nodeId)
    {
        if (!$userId) {
            return null;
        }

        $readDate = $this->_getDb()->fetchOne(
            '
			SELECT category_read_date
			FROM xf_category_read_th
			WHERE user_id = ?
				AND node_id = ?
		', array(
                $userId,
                $nodeId
            ));

        $autoReadDate = XenForo_Application::$time -
             (XenForo_Application::get('options')->readMarkingDataLifetime * 86400);
        return max($readDate, $autoReadDate);
    } /* END getUserCategoryReadDate */

    public function markCategoriesUnreadForForum(array $forum)
    {
        /* @var $nodeModel XenForo_Model_Node */
        $nodeModel = $this->getModelFromCache('XenForo_Model_Node');

        $possibleParentNodes = $nodeModel->getPossibleParentNodes($forum);
        $unreadCategoryIds = $this->getAllowedParentCategoriesForNode($forum, $possibleParentNodes);

        $db = $this->_getDb();

        $findInSets = array();
        foreach ($unreadCategoryIds as $nodeId) {
            $findInSets[] = 'NOT FIND_IN_SET(' . $db->quote($nodeId) . ', unread_category_ids_th)';
        }

        $existingCombinations = array();
        if ($findInSets) {
            $existingCombinations = $db->fetchRow(
                '
                    SELECT DISTINCT unread_category_ids_th
                    FROM xf_user_profile
                    WHERE ' . implode(' OR ', $findInSets) . '
                ');
        }

        $updates = array();
        if ($existingCombinations) {
            foreach ($existingCombinations as $combination) {
                $combinationArray = explode(',', $combination);
                $combinationArray = array_filter($combinationArray);
                $combinationArray = array_merge($combinationArray, $unreadCategoryIds);
                sort($combinationArray);
                $updates[] = 'WHEN unread_category_ids_th = ' . $db->quote($combination) . ' THEN ' .
                     $db->quote(implode(',', $combinationArray));
            }
        }

        if ($updates) {
            $db->query(
                '
                    UPDATE xf_user_profile
                    SET unread_category_ids_th = CASE
                    ' . implode(' ', $updates) . '
                    ELSE unread_category_ids_th
                    END
            ');
        }
    } /* END markCategoriesUnreadForForum */

    public function getAllowedParentCategoriesForNode(array $node, array $possibleParentNodes)
    {
        $xenOptions = XenForo_Application::get('options');

        $categoryIds = array();
        if (!empty($xenOptions->th_unreadCategories_nodeIds)) {
            $categoryIds = $xenOptions->th_unreadCategories_nodeIds;
        }

        $unreadCategoryIds = array();
        if (in_array($node['node_id'], $categoryIds)) {
            $unreadCategoryIds[] = $node['node_id'];
        }

        if (!empty($node['parent_node_id']) && !empty($possibleParentNodes[$node['parent_node_id']])) {
            $parentNode = $possibleParentNodes[$node['parent_node_id']];

            $unreadCategoryIds = array_merge($unreadCategoryIds,
                $this->getAllowedParentCategoriesForNode($parentNode, $possibleParentNodes));
        }

        return $unreadCategoryIds;
    } /* END getAllowedParentCategoriesForNode */
}