<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Model\Total\Creditmemo;

use Magento\Reward\Model\Reward as RewardModel;
use Magento\Sales\Model\Order\Creditmemo;

/**
 * Collect reward totals for credit memo.
 */
class Reward extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * @var RewardModel
     */
    private $reward;

    /**
     * @param RewardModel $reward
     * @param array $data
     */
    public function __construct(
        RewardModel $reward,
        array $data = []
    ) {
        parent::__construct($data);
        $this->reward = $reward;
    }

    /**
     * Collect reward totals for credit memo
     *
     * @param Creditmemo $creditmemo
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        $rewardCurrencyAmountLeft = $order->getRwrdCurrencyAmountInvoiced() - $order->getRwrdCrrncyAmntRefunded();
        $baseRewardCurrencyAmountLeft = $order->getBaseRwrdCrrncyAmtInvoiced() -
            $order->getBaseRwrdCrrncyAmntRefnded();
        if ($order->getBaseRewardCurrencyAmount() && $baseRewardCurrencyAmountLeft > 0) {
            if ($baseRewardCurrencyAmountLeft >= $creditmemo->getBaseGrandTotal()) {
                $rewardCurrencyAmountLeft = $creditmemo->getGrandTotal();
                $baseRewardCurrencyAmountLeft = $creditmemo->getBaseGrandTotal();
                $creditmemo->setGrandTotal(0);
                $creditmemo->setBaseGrandTotal(0);
                $creditmemo->setAllowZeroGrandTotal(true);
            } else {
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $rewardCurrencyAmountLeft);
                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseRewardCurrencyAmountLeft);
            }
            $rewardPointsBalance = $this->reward->getPointsEquivalent($baseRewardCurrencyAmountLeft);
            $rewardPointsBalanceLeft = $order->getRewardPointsBalance() - $order->getRewardPointsBalanceRefunded();
            if ($rewardPointsBalance > $rewardPointsBalanceLeft) {
                $rewardPointsBalance = $rewardPointsBalanceLeft;
            }
            $creditmemo->setRewardPointsBalance(round($rewardPointsBalance));
            $creditmemo->setRewardCurrencyAmount($rewardCurrencyAmountLeft);
            $creditmemo->setBaseRewardCurrencyAmount($baseRewardCurrencyAmountLeft);
        }

        return $this;
    }
}
