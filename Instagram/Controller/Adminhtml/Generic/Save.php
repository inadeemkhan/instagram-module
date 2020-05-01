<?php
namespace Excellence\Instagram\Controller\Adminhtml\Generic;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * 
     */
    public function __construct(
        Action\Context $context, 
        PostDataProcessor $dataProcessor,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ){
        $this->dataProcessor = $dataProcessor;
        parent::__construct($context);
        $this->_jsonHelper = $jsonHelper;
        $this->_messageManager = $messageManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Excellence_Instagram::save');
    }

    /**
     * Save action
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $data = $this->dataProcessor->filter($data);
            $model = $this->_objectManager->create('Excellence\Instagram\Model\Generic');
            $id = $this->getRequest()->getParam('generic_id');
            if ($id) {
                $model->load($id);
            }

            // save image data and remove from data array
            if (isset($data['image'])) {
                $imageData = $data['image'];
                unset($data['image']);
            } else {
                $imageData = array();
            }

            // $model->addData($data);
            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['generic_id' => $model->getId(), '_current' => true]);
                return;
            }
        try {
            $imageHelper = $this->_objectManager->get('Excellence\Instagram\Helper\Data');
            
            if (isset($imageData['delete']) && $model->getImage()) {
                $imageHelper->removeImage($model->getImage());
                $model->setImage(null);
            }
            if (array_key_exists("image-link",$data)) {
                $imageLink = $data['image-link'];
                $imageLinkJson =  $this->_jsonHelper->jsonEncode($imageLink);
                $imageLinkJson = json_encode($imageLink,true);
                $model->setImageUrls($imageLinkJson);
            }
            if (array_key_exists("stores",$data)) {
                $stores = $data['stores'];
                $storesJson =  $this->_jsonHelper->jsonEncode($stores);
                $storesJson = json_encode($stores,true);
                $model->setStoreView($storesJson);
            }
            if (array_key_exists("status",$data)) {
                $status = $data['status'];
                $model->setStatus($status);
            }
            if (array_key_exists("auto_play",$data)) {
                $auto_play = $data['auto_play'];
                $model->setAutoPlay($auto_play);
            }
            if (array_key_exists("slider_heading",$data)) {
                $slider_heading = $data['slider_heading'];
                $model->setSliderHeading($slider_heading);
            }
            if (array_key_exists("slider_subheading",$data)) {
                $slider_subheading = $data['slider_subheading'];
                $model->setSliderSubheading($slider_subheading);
            }
            if (array_key_exists("number_of_images",$data)) {
                $number_of_images = $data['number_of_images'];
                $model->setNumberOfImages($number_of_images);
            }
            if (array_key_exists("time_interval",$data)) {
                $time_interval = $data['time_interval'];
                $model->setTimeInterval($time_interval);
            }
            if (array_key_exists("searchImage",$data)) {
                $searchImage = $data['searchImage'];
                $model->setSearchImage($searchImage);
            }

            if (array_key_exists("instaImgUrl",$data)) {
                $imageLink = $data['instaImgUrl'];
                $imageLinkJson =  $this->_jsonHelper->jsonEncode($imageLink);
                $imageLinkJson = json_encode($imageLink,true);
                $model->setImageUrls($imageLinkJson);
            }
            if (array_key_exists("imageTagName",$data)) {
                $imageTagName = $data['imageTagName'];
                $imageTagNameJson =  $this->_jsonHelper->jsonEncode($imageTagName);
                $imageTagNameJson = json_encode($imageTagName,true);
                $model->setImageTag($imageTagNameJson);
            }
            if (array_key_exists("comments",$data)) {
                $comments = $data['comments'];
                $commentsJson =  $this->_jsonHelper->jsonEncode($comments);
                $commentsJson = json_encode($comments,true);
                $model->setComments($commentsJson);
            }
            if (array_key_exists("likes",$data)) {
                $likes = $data['likes'];
                $likesJson =  $this->_jsonHelper->jsonEncode($likes);
                $likesJson = json_encode($likes,true);
                $model->setLikes($likesJson);
            }
            if (array_key_exists("postLink",$data)) {
                $postLink = $data['postLink'];
                $postLinkJson =  $this->_jsonHelper->jsonEncode($postLink);
                $postLinkJson = json_encode($postLink,true);
                $model->setPostLink($postLinkJson);
            }
            if (array_key_exists("username",$data)) {
                $username = $data['username'];
                $model->setUsername($username);
            }
            
            if(array_key_exists("instaImgUrl",$data)){
                $model->save();
                $this->messageManager->addSuccess(__('The generic slider has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['generic_id' => $model->getId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                }else{
                    if ($id){
                        $this->_redirect('*/*/edit', ['generic_id' => $model->getId(), '_current' => true]);
                    }else{
                        $this->_redirect('*/*/new');
                    }
                }
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the generic slider.'));
            }
            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['generic_id' => $this->getRequest()->getParam('generic_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
}
