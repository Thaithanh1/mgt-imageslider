<?php

namespace Tam\BannerSlider\Model\Banner;

use Tam\BannerSlider\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Tam\BannerSlider\Model\Banner\FileInfo;
use Magento\Framework\Filesystem;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Tam\BannerSlider\Model\ResourceModel\Banner\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var Filesystem
     */
    private $fileInfo;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $bannerCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $bannerCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $bannerCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \Tam\BannerSlider\Model\Banner $banner */
        foreach ($items as $banner) {
            $banner = $this->convertValues($banner);
            $this->loadedData[$banner->getId()] = $banner->getData();
        }

        // Used from the Save action
        $data = $this->dataPersistor->get('banner_slider');
        if (!empty($data)) {
            $banner = $this->collection->getNewEmptyItem();
            $banner->setData($data);
            $this->loadedData[$banner->getId()] = $banner->getData();
            $this->dataPersistor->clear('banner_slider');
        }

        return $this->loadedData;
    }

    /**
     * Converts image data to acceptable for rendering format
     *
     * @param \Tam\BannerSlider\Model\Banner $banner
     * @return \Tam\BannerSlider\Model\Banner $banner
     */
    private function convertValues($banner)
    {
        $fileName = $banner->getImage();
        $image = [];
        if ($this->getFileInfo()->isExist($fileName)) {
            $stat = $this->getFileInfo()->getStat($fileName);
            $mime = $this->getFileInfo()->getMimeType($fileName);
            $image[0]['name'] = $fileName;
            $image[0]['url'] = $banner->getImageUrl();
            $image[0]['size'] = isset($stat) ? $stat['size']: 0;
            $image[0]['type'] = $mime;
        }
        $banner->setImage($image);

        return $banner;
    }

    /**
     * Get FileInfo instance
     *
     * @return FileInfo
     *
     * @deprecated 101.1.0
     */
    private function getFileInfo()
    {
        if ($this->fileInfo === null) {
            $this->fileInfo = ObjectManager::getInstance()->get(FileInfo::class);
        }
        return $this->fileInfo;
    }
}
