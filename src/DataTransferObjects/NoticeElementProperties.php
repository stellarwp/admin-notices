<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\DataTransferObjects;

use InvalidArgumentException;
use StellarWP\AdminNotices\AdminNotice;

class NoticeElementProperties
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    public $idAttribute;

    public $customCloserAttribute;

    /**
     * @var string
     */
    public $customLocationAttribute;

    /**
     * @var string
     */
    public $customWrapperAttributes;

    /**
     * @unreleased
     */
    public function __construct(AdminNotice $notice, string $namespace)
    {
        $this->namespace = $namespace;

        $this->idAttribute = "data-stellarwp-$namespace-notice-id='{$notice->getId()}'";

        $this->customLocationAttribute = "data-stellarwp-$namespace-location='{$notice->getLocation()}'";
        $this->customWrapperAttributes = "$this->idAttribute $this->customLocationAttribute";

        $this->customCloserAttribute = "data-stellarwp-$namespace-close-notice='{$notice->getId()}'";
    }

    /**
     * @unreleased
     *
     * @param 'hide'|'clear' $behavior
     */
    public function customCloseBehaviorAttribute(string $behavior = 'hide'): string
    {
        if (!in_array($behavior, ['hide', 'clear'], true)) {
            throw new InvalidArgumentException('Invalid behavior for custom closer attribute.');
        }

        return "data-stellarwp-{$this->namespace}-close-notice-behavior='$behavior'";
    }

    /**
     * @unreleased
     *
     * @param 'hide'|'clear' $behavior
     */
    public function customCloserAttributes(string $behavior = 'hide'): string
    {
        if (!in_array($behavior, ['hide', 'clear'], true)) {
            throw new InvalidArgumentException('Invalid behavior for custom closer attribute.');
        }

        return "$this->customCloserAttribute {$this->customCloseBehaviorAttribute($behavior)}";
    }
}
