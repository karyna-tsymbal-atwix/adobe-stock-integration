<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAsset\Model\SaveAsset;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockImageApi\Api\SaveImageInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SaveImage
 */
class SaveImage implements SaveImageInterface
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SaveAsset
     */
    private $saveAsset;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * SaveImagePreview constructor.
     * @param SaveAsset $saveAsset
     * @param Storage $storage
     * @param LoggerInterface $logger
     * @param ClientInterface $client
     */
    public function __construct(
        SaveAsset $saveAsset,
        Storage $storage,
        LoggerInterface $logger,
        ClientInterface $client
    ) {
        $this->storage = $storage;
        $this->logger = $logger;
        $this->saveAsset = $saveAsset;
        $this->client = $client;
    }

    /**
     * @inheritdoc
     */
    public function execute(AssetInterface $asset, string $destinationPath): void
    {
        try {
            $path = $this->storage->save($asset->getUrl(), $destinationPath);
            $asset->setPath($path);
            $this->saveAsset->execute($asset);
        } catch (\Exception $exception) {
            $message = __('Image was not saved: %1', $exception->getMessage());
            $this->logger->critical($message);
            throw new CouldNotSaveException($message);
        }
    }
}
