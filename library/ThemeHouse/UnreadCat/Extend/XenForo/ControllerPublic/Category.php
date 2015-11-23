<?php

/**
 *
 * @see XenForo_ControllerPublic_Category
 */
class ThemeHouse_UnreadCat_Extend_XenForo_ControllerPublic_Category extends XFCP_ThemeHouse_UnreadCat_Extend_XenForo_ControllerPublic_Category
{

    /**
     *
     * @see XenForo_ControllerPublic_Category::actionIndex()
     */
    public function actionIndex()
    {
        $response = parent::actionIndex();

        if ($response instanceof XenForo_ControllerResponse_View) {
            $category = $response->params['category'];

            $messageCount = 0;
            $readDate = 0;

            $nodeList = $response->params['nodeList'];
            if (!empty($nodeList['nodesGrouped'][$category['node_id']])) {
                foreach ($nodeList['nodesGrouped'][$category['node_id']] as $nodeId => $node) {
                    $messageCount += $node['message_count'];
                    if ($node['last_post_date'] > $readDate) {
                        $readDate = $node['last_post_date'];
                    }
                }
            }

            if ($messageCount && $readDate) {
                $this->_getCategoryModel()->markCategoryRead($category, $readDate, $messageCount);
            }
        }

        return $response;
    } /* END actionIndex */
}