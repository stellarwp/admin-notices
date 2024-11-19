<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\DataTransferObjects;

use InvalidArgumentException;
use StellarWP\AdminNotices\AdminNotice;

class NoticeElementProperties
{
    /**
     * @var string custom namespace for the library instance
     */
    private $namespace;

    /**
     * @var string id attribute that uniquely identifies the notice
     */
    public $idAttribute;

    /**
     * @var string attribute to put on an element which closes the notice
     */
    public $customCloserAttribute;

    /**
     * @var string attribute for custom notices which specifies the location
     */
    public $customLocationAttribute;

    /**
     * @var string attributes to be applied to custom notices
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
    public function closeAttributes(string $behavior = 'hide'): string
    {
        if (!in_array($behavior, ['hide', 'clear'], true)) {
            throw new InvalidArgumentException('Invalid behavior for custom closer attribute.');
        }

        return "$this->customCloserAttribute {$this->customCloseBehaviorAttribute($behavior)}";
    }
}
