<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Reward\Model\Plugin;

use Magento\CustomerBalance\Model\Creditmemo\Balance;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Creditmemo;

/**
 * Plugin checks if refunded customer balance and reward points do not exceed available balance.
 */
class CustomerBalance
{
    /**
     * @var \Magento\Reward\Model\Reward
     */
    private $reward;

    /**
     * @param \Magento\Reward\Model\Reward $reward
     */
    public function __construct(
        \Magento\Reward\Model\Reward $reward
    ) {
        $this->reward = $reward;
    }

    /**
     * Before customer balance save processing.
     *
     * @param Balance $subject
     * @param Creditmemo $creditmemo
     * @return string|null
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(
        Balance $subject,
        Creditmemo $creditmemo
    ) {
        if (!$this->isBalanceAvailable($creditmemo)) {
            throw new LocalizedException(__('You can\'t use more store credit than the order amount.'));
        }

        return null;
    }

    /**
     * Checks if refunded amount does not exceed available balance.
     *
     * @param Creditmemo $creditmemo
     * @return bool
     */
    private function isBalanceAvailable(Creditmemo $creditmemo): bool
    {
        $refundedToRewardPoints = $creditmemo->getRewardPointsBalanceRefund();
        $refundedToCustomerBalance = $this->reward->getPointsEquivalent(
            (float) $creditmemo->getBsCustomerBalTotalRefunded()
        );

        $rewardPointsCeilCompensation = 1;
        $availableBalance = $this->reward->getPointsEquivalent(
            (float) $creditmemo->getBaseCustomerBalanceReturnMax() + $rewardPointsCeilCompensation
        );

        return $refundedToRewardPoints + $refundedToCustomerBalance <= $availableBalance;
    }
}
