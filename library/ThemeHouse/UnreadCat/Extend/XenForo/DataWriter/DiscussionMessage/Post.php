<?php

/**
 *
 * @see XenForo_DataWriter_DiscussionMessage_Post
 */
class ThemeHouse_UnreadCat_Extend_XenForo_DataWriter_DiscussionMessage_Post extends XFCP_ThemeHouse_UnreadCat_Extend_XenForo_DataWriter_DiscussionMessage_Post
{

    /**
     * Post-save handling.
     */
    protected function _messagePostSave()
    {
        parent::_messagePostSave();

        if ($this->isInsert()) {
            $forum = $this->_getForumInfo();

            $categoryModel = $this->getModelFromCache('XenForo_Model_Category');
            $categoryModel->markCategoriesUnreadForForum($forum);
        }
    } /* END _messagePostSave */
}