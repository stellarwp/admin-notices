<?php

declare(strict_types=1);

use StellarWP\AdminNotices\Actions\DisplayNoticesInAdmin;
use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\Tests\TestCase;

class DisplayNoticesInAdminTest extends TestCase
{
    /**
     * @unreleased
     */
    public function testShouldEchoNothingWithNoNotices(): void
    {
        $displayNoticesInAdmin = new DisplayNoticesInAdmin();

        $this->expectOutputString('');
        $displayNoticesInAdmin();
    }

    /**
     * @unreleased
     */
    public function testShouldAcceptMultipleNotices(): void
    {
        $displayNoticesInAdmin = new DisplayNoticesInAdmin();
        $notice1 = $this->getSimpleMockNotice('foo');
        $notice2 = $this->getSimpleMockNotice('bar');

        $this->expectOutputString('foobar');
        $displayNoticesInAdmin($notice1, $notice2);
    }

    /**
     * @dataProvider passDateLimitsDataProvider
     *
     * @unreleased
     */
    public function testPassesDateLimits(AdminNotice $notice, $passes): void
    {
        $displayNoticesInAdmin = new DisplayNoticesInAdmin();

        if ($passes) {
            $this->expectOutputString('foo');
        } else {
            $this->expectOutputString('');
        }

        $displayNoticesInAdmin($notice);
    }

    public function passDateLimitsDataProvider(): array
    {
        return [
            'Passes with no date limits' => [$this->getSimpleMockNotice('foo'), true],
            'Passes with valid after date' => [$this->getSimpleMockNotice('foo')->after('yesterday'), true],
            'Passes with valid until date' => [$this->getSimpleMockNotice('foo')->until('tomorrow'), true],
            'Passes with valid between date limits' => [
                $this->getSimpleMockNotice('foo')->between('yesterday', 'tomorrow'),
                true,
            ],
            'Fails with invalid after date' => [$this->getSimpleMockNotice('foo')->after('tomorrow'), false],
            'Fails with invalid until date' => [$this->getSimpleMockNotice('foo')->until('yesterday'), false],
            'Fails with invalid between date limits' => [
                $this->getSimpleMockNotice('foo')->between('tomorrow', 'yesterday'),
                false,
            ],
        ];
    }

    /**
     * Produces a simple mock with predictable output.
     *
     * @unreleased
     */
    private function getSimpleMockNotice($output): AdminNotice
    {
        return (new AdminNotice($output))
            ->withoutWrapper()
            ->withoutAutoParagraph();
    }
}
