<?php

namespace Tam\BannerSlider\Model\Banner\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    /**
     * @var \Tam\BannerSlider\Model\Banner
     */
    protected $banner;

    /**
     * Constructor
     *
     * @param \Tam\BannerSlider\Model\Banner $banner
     */
    public function __construct(\Tam\BannerSlider\Model\Banner $banner)
    {
        $this->banner = $banner;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->banner->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
