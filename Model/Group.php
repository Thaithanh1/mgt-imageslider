<?php

namespace Tam\BannerSlider\Model;

use Magento\Framework\Exception\LocalizedException;

class Group extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Group cache tag
     */
    const CACHE_TAG = 'tam_banners_slider_group';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Tam\BannerSlider\Model\ResourceModel\Group::class);
    }

    /**
     * Processing object before save data
     *
     * @return $this
     */
    public function beforeSave()
    {
        $groupName = $this->getData('name');
        $groupId = (int)$this->getData('id');
        $collection = $this->getCollection()->addFieldToFilter('name', $groupName);
        if ($groupId) {
            $collection = $collection->addFieldToFilter('id', ['neq' => $groupId]);
        }
        $group = $collection->getFirstItem();
        if ($group->getId()) {
            throw new LocalizedException(__('The Group Name has already existed.'));
        }
        parent::beforeSave();
        return $this;
    }
}
