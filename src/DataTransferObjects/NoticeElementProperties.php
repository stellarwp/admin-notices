<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\DataTransferObjects;

use StellarWP\AdminNotices\AdminNotice;

class NoticeElementProperties
{
    /**
     * @var string
     */
    public $idAttribute;

    /**
     * @var string
     */
    public $closerIdentifierClass;

    /**
     * @var string
     */
    public $closerHideClass;

    /**
     * @var string[]
     */
    public $closerClasses;

    /**
     * @var string
     */
    public $customNoticeClass;

    /**
     * @var string
     */
    public $customLocationClass;

    /**
     * @var string
     */
    public $customLocationAttribute;

    public function __construct(AdminNotice $notice, string $namespace)
    {
        $this->idAttribute = "data-stellarwp-$namespace-notice-id='{$notice->getId()}'";
        $this->closerIdentifierClass = "js-stellarwp-$namespace-close-notice";
        $this->closerHideClass = "js-stellarwp-$namespace-close-notice--hide";

        $this->customNoticeClass = "stellarwp-$namespace-notice-custom";
        $this->customLocationClass = "stellarwp-$namespace-location-{$notice->getLocation()}";
        $this->customLocationAttribute = "data-stellarwp-$namespace-location='{$notice->getLocation()}'";

        $this->closerClasses = $notice->isDismissible() ? [
            $this->closerIdentifierClass,
            $this->closerHideClass,
        ] : [];
    }
}
