<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\DataTransferObjects;

use InvalidArgumentException;
use StellarWP\AdminNotices\AdminNotice;

class NoticeElementProperties
{
    private const CLOSE_BEHAVIOR_HIDE = 'hide';
    private const CLOSE_BEHAVIOR_MARK_DISMISSED = 'mark-dismissed';

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
     * @param 'hide'|'mark-dismissed' $behavior
     */
    public function customCloseBehaviorAttribute(string $behavior = self::CLOSE_BEHAVIOR_HIDE): string
    {
        if (!$this->behaviorIsValid($behavior)) {
            throw new InvalidArgumentException('Invalid behavior for custom closer attribute.');
        }

        return "data-stellarwp-{$this->namespace}-close-notice-behavior='$behavior'";
    }

    /**
     * @unreleased
     *
     * @param 'hide'|'mark-dismissed' $behavior
     */
    public function closeAttributes(string $behavior = self::CLOSE_BEHAVIOR_HIDE): string
    {
        if (!$this->behaviorIsValid($behavior)) {
            throw new InvalidArgumentException('Invalid behavior for custom closer attribute.');
        }

        return "$this->customCloserAttribute {$this->customCloseBehaviorAttribute($behavior)}";
    }

    /**
     * @unreleased
     */
    private function behaviorIsValid(string $behavior): bool
    {
        return in_array($behavior, [self::CLOSE_BEHAVIOR_HIDE, self::CLOSE_BEHAVIOR_MARK_DISMISSED], true);
    }
}
