<?php

namespace Excellence\Instagram\Block;

/**
 * Instagram content block
 */
class Instagram extends \Magento\Framework\View\Element\Template
{
    /**
     * Instagram collection
     *
     * @var Excellence\Instagram\Model\ResourceModel\Instagram\Collection
     */
    protected $_instagramCollection = null;
    
    /**
     * Instagram factory
     *
     * @var \Excellence\Instagram\Model\InstagramFactory
     */
    protected $_instagramCollectionFactory;
    
    /** @var \Excellence\Instagram\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Excellence\Instagram\Model\ResourceModel\Instagram\CollectionFactory $instagramCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Excellence\Instagram\Model\ResourceModel\Instagram\CollectionFactory $instagramCollectionFactory,
        \Excellence\Instagram\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_instagramCollectionFactory = $instagramCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve instagram collection
     *
     * @return Excellence\Instagram\Model\ResourceModel\Instagram\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_instagramCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared instagram collection
     *
     * @return Excellence\Instagram\Model\ResourceModel\Instagram\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_instagramCollection)) {
            $this->_instagramCollection = $this->_getCollection();
            $this->_instagramCollection->setCurPage($this->getCurrentPage());
            $this->_instagramCollection->setPageSize($this->_dataHelper->getInstagramPerPage());
            $this->_instagramCollection->setOrder('published_at','asc');
        }

        return $this->_instagramCollection;
    }
    
    /**
     * Fetch the current page for the instagram list
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getData('current_page') ? $this->getData('current_page') : 1;
    }
    
    /**
     * Return URL to item's view page
     *
     * @param Excellence\Instagram\Model\Instagram $instagramItem
     * @return string
     */
    public function getItemUrl($instagramItem)
    {
        return $this->getUrl('*/*/view', array('id' => $instagramItem->getId()));
    }
    
    /**
     * Return URL for resized Instagram Item image
     *
     * @param Excellence\Instagram\Model\Instagram $item
     * @param integer $width
     * @return string|false
     */
    public function getImageUrl($item, $width)
    {
        return $this->_dataHelper->resize($item, $width);
    }
    
    /**
     * Get a pager
     *
     * @return string|null
     */
    public function getPager()
    {
        $pager = $this->getChildBlock('instagram_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $instagramPerPage = $this->_dataHelper->getInstagramPerPage();

            $pager->setAvailableLimit([$instagramPerPage => $instagramPerPage]);
            $pager->setTotalNum($this->getCollection()->getSize());
            $pager->setCollection($this->getCollection());
            $pager->setShowPerPage(TRUE);
            $pager->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            );

            return $pager->toHtml();
        }

        return NULL;
    }
}
