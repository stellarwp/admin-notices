<?php

declare(strict_types=1);

namespace StellarWP\AdminNotice;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use StellarWP\AdminNotice\ValueObjects\NoticeUrgency;
use StellarWP\AdminNotice\ValueObjects\ScreenCondition;

class AdminNotice
{
    /**
     * @var string|callable
     */
    protected $renderTextOrCallback;

    /**
     * @var array capability arguments compatible with current_user_can()
     *
     * @see https://developer.wordpress.org/reference/functions/current_user_can/
     */
    protected $userCapabilities;

    /**
     * @var DateTimeInterface
     */
    protected $afterDate;

    /**
     * @var DateTimeInterface
     */
    protected $untilDate;

    /**
     * @var callable
     */
    protected $whenCallback;

    /**
     * @var array
     */
    protected $onConditions;

    /**
     * @var bool
     */
    protected $autoParagraph = true;

    /**
     * @var NoticeUrgency
     */
    protected $urgency;

    /**
     * @var bool
     */
    protected $withContainer = true;

    protected $dismissible = true;

    /**
     * @unreleased
     *
     * @param string|callable $renderTextOrCallback
     */
    public function __construct($renderTextOrCallback)
    {
        if (!is_string($renderTextOrCallback) && !is_callable($renderTextOrCallback)) {
            throw new InvalidArgumentException('The renderTextOrCallback argument must be a string or a callable');
        }

        $this->renderTextOrCallback = $renderTextOrCallback;
        $this->urgency = NoticeUrgency::info();
    }

    /**
     * Limits the notice to display based on the capabilities of the current user
     *
     * @unreleased
     *
     * @param string|array ...$capabilities String or array of arguments compatible with current_user_can()
     *
     * @return $this
     */
    public function ifUserCan(...$capabilities): self
    {
        $this->userCapabilities = [];

        // Validate and store the capabilities
        foreach ($capabilities as $capability) {
            if (is_string($capability)) {
                $this->userCapabilities[] = [$capability];
            } elseif (is_array($capability) && (count($capability) === 2 || count($capability) === 3) && is_string(
                    $capability[0]
                )) {
                $this->userCapabilities = $capability;
            } else {
                throw new InvalidArgumentException(
                    'Invalid capability type. Must be string or array of arguments compatible with current_user_can()'
                );
            }
        }

        return $this;
    }

    /**
     * Limits the notice to display after a specific date
     *
     * @unreleased
     *
     * @param $date DateTimeInterface|string
     *
     * @return $this
     * @throws Exception If the date is not a valid DateTimeInterface or string
     */
    public function after($date): self
    {
        $this->afterDate = $date instanceof DateTimeInterface ? $date : new DateTimeImmutable($date);

        return $this;
    }

    /**
     * Limits the notice to display until a specific date
     *
     * @unreleased
     *
     * @param $date DateTimeInterface|string
     *
     * @throws Exception If the date is not a valid DateTimeInterface or string
     */
    public function until($date): self
    {
        $this->untilDate = $date instanceof DateTimeInterface ? $date : new DateTimeImmutable($date);

        return $this;
    }

    /**
     * Limits the notice to a specific date range
     *
     * @param $after DateTimeInterface|string
     * @param $until DateTimeInterface|string
     *
     * @throws Exception If the date is not a valid DateTimeInterface or string
     */
    public function between($after, $until): self
    {
        return $this->after($after)->until($until);
    }

    /**
     * Provide a callback which returns a boolean to determine if the notice should be displayed
     *
     * @unreleased
     */
    public function when(callable $callback): self
    {
        $this->whenCallback = $callback;

        return $this;
    }

    /**
     * Limits the notice to display on specific screens
     *
     * @unreleased
     *
     * @param array|string $on
     */
    public function on($on): self
    {
        // if $on is an array of conditions, create a ScreenCondition for each
        if (is_array($on) && array_keys($on) === range(0, count($on) - 1)) {
            foreach ($on as $condition) {
                $this->onConditions[] = new ScreenCondition($condition);
            }
        }

        $this->onConditions[] = new ScreenCondition($on);

        return $this;
    }

    /**
     * Automatically applies paragraph tags to the notice content
     *
     * @unreleased
     */
    public function autoParagraph(bool $auto = true): self
    {
        $this->autoParagraph = $auto;

        return $this;
    }

    /**
     * Disables automatic paragraph tagging
     *
     * @unreleased
     */
    public function withoutAutoParagraph(): self
    {
        $this->autoParagraph = false;

        return $this;
    }

    /**
     * Sets the urgency of the notice, used when the notice is displayed in the standard container
     *
     * @unreleased
     *
     * @param $urgency string|NoticeUrgency
     */
    public function urgency($urgency): self
    {
        $this->urgency = $urgency instanceof NoticeUrgency ? $urgency : new NoticeUrgency($urgency);

        return $this;
    }

    /**
     * Sets the notice to display without the standard WordPress container
     *
     * @unreleased
     */
    public function withContainer(bool $withContainer = true): self
    {
        $this->withContainer = $withContainer;

        return $this;
    }

    /**
     * Sets the notice to display without the standard WordPress container
     *
     * @unreleased
     */
    public function withoutContainer(): self
    {
        $this->withContainer = false;

        return $this;
    }

    /**
     * Sets the notice to be dismissible, usable when the notice is displayed in the standard container
     *
     * @unreleased
     */
    public function dismissible(bool $dismissible = true): self
    {
        $this->dismissible = $dismissible;

        return $this;
    }

    /**
     * Sets the notice to be not dismissible, usable when the notice is displayed in the standard container
     *
     * @unreleased
     */
    public function notDismissible(): self
    {
        $this->dismissible = false;

        return $this;
    }

    /**
     * Returns the text or callback used to render the notice
     *
     * @unreleased
     *
     * @return callable|string
     */
    public function getRenderTextOrCallback()
    {
        return $this->renderTextOrCallback;
    }

    /**
     * Returns the user capabilities
     *
     * @unreleased
     */
    public function getUserCapabilities(): array
    {
        return $this->userCapabilities;
    }

    /**
     * Returns the date after which the notice should be displayed
     *
     * @unreleased
     */
    public function getAfterDate(): DateTimeInterface
    {
        return $this->afterDate;
    }

    /**
     * Returns the date until which the notice should be displayed
     *
     * @unreleased
     */
    public function getUntilDate(): DateTimeInterface
    {
        return $this->untilDate;
    }

    /**
     * Returns the callback used to determine if the notice should be displayed
     *
     * @unreleased
     */
    public function getWhenCallback(): callable
    {
        return $this->whenCallback;
    }

    /**
     * Returns the screen conditions used to determine if the notice should be displayed
     *
     * @unreleased
     */
    public function getOnConditions(): array
    {
        return $this->onConditions;
    }

    /**
     * Returns whether the notice content should be automatically wrapped in paragraph tags
     *
     * @unreleased
     */
    public function isAutoParagraph(): bool
    {
        return $this->autoParagraph;
    }

    /**
     * Returns the urgency of the notice
     *
     * @unreleased
     */
    public function getUrgency(): NoticeUrgency
    {
        return $this->urgency;
    }

    /**
     * Returns whether the notice should be displayed with the standard WordPress container
     *
     * @unreleased
     */
    public function isWithContainer(): bool
    {
        return $this->withContainer;
    }

    /**
     * Returns whether the notice is dismissible
     *
     * @unreleased
     */
    public function isDismissible(): bool
    {
        return $this->dismissible;
    }
}