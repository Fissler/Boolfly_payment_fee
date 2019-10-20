<?php

/**
 * Copyright (c) 2008-2019 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Julian Wundrak - initial contents
 */

namespace Boolfly\PaymentFee\Plugin\Order;

use Magento\Sales\Api\Data\OrderExtensionInterfaceFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

/**
 * Class AddPaymentFeePlugin
 *
 * @package Boolfly\PaymentFee\Plugin\Order
 */
class AddPaymentFeePlugin
{
    /**
     * @var OrderExtensionInterfaceFactory
     */
    private $extensionAttributesFactory;

    /**
     * AddErpEntityIdPlugin constructor.
     *
     * @param OrderExtensionInterfaceFactory $extensionAttributesFactory
     */
    public function __construct(OrderExtensionInterfaceFactory $extensionAttributesFactory)
    {
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    /**
     * Add sap_entity_id to extension attributes
     *
     * @param OrderRepositoryInterface   $subject
     * @param OrderSearchResultInterface $results
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $results)
    {
        $orders = $results->getItems();
        $this->addPaymentFeeToOrders($orders);
        return $results;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface           $order
     * @return OrderInterface`
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        $orders = $this->addPaymentFeeToOrders([$order]);
        return array_shift($orders);
    }

    /**
     * @param OrderInterface[] $orders
     * @return OrderInterface[]
     */
    protected function addPaymentFeeToOrders(array $orders): array
    {
        foreach ($orders as $order) {
            /** @var Order $order */
            if ($amount = $order->getFeeAmount()) {
                if (!$extensionAttributes = $order->getExtensionAttributes()) {
                    $extensionAttributes = $this->extensionAttributesFactory->create();
                }
                $extensionAttributes->setFeeAmount($amount);
                $extensionAttributes->setBaseFeeAmount($order->getBaseFeeAmount());
                $order->setExtensionAttributes($extensionAttributes);
            }
        }

        return $orders;
    }
}
