<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Plugin\Framework\Session;

/**
 * Plugin for SID resolver.
 *
 * @deprecated 101.1.4 The raw session ID should not be used for PCI compliance
 */
class SidResolver
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Staging\Model\VersionManager
     */
    private $versionManager;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Staging\Model\VersionManager $versionManager
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Staging\Model\VersionManager $versionManager
    ) {
        $this->request = $request;
        $this->versionManager = $versionManager;
    }

    /**
     * Resolves the session from the query string
     *
     * @param \Magento\Framework\Session\SidResolver $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Session\SessionManagerInterface $session
     *
     * @return string|null
     */
    public function aroundGetSid(
        \Magento\Framework\Session\SidResolver $subject,
        \Closure $proceed,
        \Magento\Framework\Session\SessionManagerInterface $session
    ) {
        if ($this->versionManager->isPreviewVersion()) {
            return $this->request->getQuery(
                $subject->getSessionIdQueryParam($session)
            );
        }

        return $proceed($session);
    }
}
