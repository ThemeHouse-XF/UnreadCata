<?php

/**
 * Handles alerts of unread categories.
 *
 * @package XenForo_Alert
 */
class ThemeHouse_UnreadCat_AlertHandler_UnreadCategory extends XenForo_AlertHandler_DiscussionMessage
{

    /**
     *
     * @var XenForo_Model_Node
     */
    protected $_nodeModel = null;

    /**
     *
     * @var XenForo_Model_Category
     */
    protected $_categoryModel = null;

    /**
     * Gets the unread category content.
     * @see XenForo_AlertHandler_Abstract::getContentByIds()
     */
    public function getContentByIds(array $contentIds, $model, $userId, array $viewingUser)
    {
        $nodeModel = $this->_getNodeModel();

        $nodeData = $nodeModel->getNodeDataForListDisplay(false, 0);

        $nodes = array();
        foreach ($nodeData['nodesGrouped'] as $parentNodeId => $groupedNodes) {
            foreach ($groupedNodes as $nodeId => $node) {
                if ($node['node_type_id'] == 'Category' && !empty($node['last_post_date'])) {
                    $nodes[$nodeId] = $node;
                }
            }
        }

        return $nodes;
    } /* END getContentByIds */

    /**
     * Determines if the unread category is viewable.
     * @see XenForo_AlertHandler_Abstract::canViewAlert()
     */
    public function canViewAlert(array $alert, $content, array $viewingUser)
    {
        return $this->_getCategoryModel()->canViewCategory($content);
    } /* END canViewAlert */

    /**
     *
     * @return XenForo_Model_Category
     */
    protected function _getCategoryModel()
    {
        if (!$this->_categoryModel) {
            $this->_categoryModel = XenForo_Model::create('XenForo_Model_Category');
        }

        return $this->_categoryModel;
    } /* END _getCategoryModel */

    /**
     *
     * @return XenForo_Model_Node
     */
    protected function _getNodeModel()
    {
        if (!$this->_nodeModel) {
            $this->_nodeModel = XenForo_Model::create('XenForo_Model_Node');
        }

        return $this->_nodeModel;
    } /* END _getNodeModel */
}