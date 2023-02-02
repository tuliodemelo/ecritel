<?php
/**
 * @author      Tulio de Melo | Caravel Core Team
 * @copyright   2023 Caravel (https://caravel.com.br)
 * @license     Caravel Proprietary
 * @link        https://caravel.com.br
 */
declare(strict_types=1);

namespace Ecritel\CmsPageAuthor\Plugin;

use Magento\Cms\Model\Page;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class AddPageAuthor
{
    const XML_PATH_IS_VISIBLE_ON_FRONT = 'author/general/is_visible_on_storefront';

    protected ScopeConfigInterface $scopeConfig;

    protected StoreManagerInterface $storeManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Page $subject
     * @param $result
     * @return string
     */
    public function afterGetContent(Page $subject, $result): string
    {
        $authorName = $subject->getAuthor();

        if (!$this->isVisibleOnFront() || empty($authorName)) {
            return $result;
        }

        $authorHtml = "<article><header>Author: <a href='#' rel='author'>$authorName</a></header></article>";
        return $authorHtml . $result;
    }

    /**
     * @return bool
     */
    public function isVisibleOnFront(): bool
    {
        $storeId = $this->getStoreId();
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_IS_VISIBLE_ON_FRONT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        try {
            return (int) $this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}
