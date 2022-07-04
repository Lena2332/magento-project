<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Model;

/**
 * @method int|string|null getRegularCustomerId()
 * @method int|string|null getProductId()
 * @method $this setProductId(int|null $productId)
 * @method int|string|null getCustomerId()
 * @method $this setCustomerId(int $customerId)
 * @method int|string|null getAdminUserId()
 * @method $this setAdminUserId(int $adminUserId)
 * @method string|null getName()
 * @method $this setName(string $name)
 * @method string|null getEmail()
 * @method $this setEmail(string $name)
 * @method int|string|null getStoreId()
 * @method $this setStoreId(int $websiteId)
 * @method int|null getUserId()
 * @method $this setUserId(int $userId)
 * @method int|string|null getCreatedAt()
 * @method int|string|null getUpdatedAt()
 * @method int|string getStatus()
 * @method $this setStatus(int $status)
 * @method int|string getEmailSent()
 * @method $this setEmailSent(int $emailSent)
 * @method int|string|null getStatusChangedAt()
 * @method $this setStatusChangedAt(string $statusChangedAt)
 */
class DiscountRequest extends \Magento\Framework\Model\AbstractModel
{
    public const STATUS_PENDING = 1;
    public const STATUS_APPROVED = 2;
    public const STATUS_DECLINED = 3;

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(\OlenaK\RegularCustomer\Model\ResourceModel\DiscountRequest::class);
    }
}
