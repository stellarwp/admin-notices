<?php

declare(strict_types=1);

use StellarWP\AdminNotices\Actions\DisplayNoticesInAdmin;
use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\Tests\Support\Helper\TestCase;
use StellarWP\AdminNotices\Tests\Support\Traits\WithUopz;
use StellarWP\AdminNotices\ValueObjects\UserCapability;

/**
 * @coversDefaultClass \StellarWP\AdminNotices\Actions\DisplayNoticesInAdmin
 */
class DisplayNoticesInAdminTest extends TestCase
{
    use WithUopz;

    /**
     * @var array $originalServer The original $_SERVER superglobal, for restoration after tests.
     */
    protected $originalServer;

    /**
     * @since 1.0.0
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->originalServer = $_SERVER;
    }

    /**
     * @since 1.0.0
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $_SERVER = $this->originalServer;
    }

    /**
     * @since 1.0.0
     */
    public function testShouldEchoNothingWithNoNotices(): void
    {
        $displayNoticesInAdmin = new DisplayNoticesInAdmin();

        $this->expectOutputString('');
        $displayNoticesInAdmin();
    }

    /**
     * @since 1.0.0
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
     * @covers ::passesDateLimits
     * @dataProvider passDateLimitsDataProvider
     *
     * @since 1.0.0
     */
    public function testPassesDateLimits(AdminNotice $notice, bool $shouldPass): void
    {
        $displayNoticesInAdmin = new DisplayNoticesInAdmin();

        if ($shouldPass) {
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
     * @dataProvider passWhenCallbackDataProvider
     * @covers ::passesWhenCallback
     *
     * @since 1.0.0
     */
    public function testPassesWhenCallback(AdminNotice $notice, bool $shouldPass): void
    {
        $displayNoticesInAdmin = new DisplayNoticesInAdmin();

        if ($shouldPass) {
            $this->expectOutputString('foo');
        } else {
            $this->expectOutputString('');
        }

        $displayNoticesInAdmin($notice);
    }

    /**
     * @since 1.0.0
     */
    public function passWhenCallbackDataProvider(): array
    {
        return [
            'Passes with no callback' => [$this->getSimpleMockNotice('foo'), true],
            'Passes with valid callback' => [
                $this->getSimpleMockNotice('foo')->when(function () {
                    return true;
                }),
                true,
            ],
            'Fails with invalid callback' => [
                $this->getSimpleMockNotice('foo')->when(function () {
                    return false;
                }),
                false,
            ],
        ];
    }

    /**
     * @dataProvider passUserCapabilitiesDataProvider
     * @covers ::passesUserCapabilities
     *
     * @since 1.0.0
     */
    public function testPassesUserCapabilities(AdminNotice $notice, bool $shouldPass): void
    {
        $displayNoticesInAdmin = new DisplayNoticesInAdmin();

        if ($shouldPass) {
            $this->expectOutputString('foo');
        } else {
            $this->expectOutputString('');
        }

        $displayNoticesInAdmin($notice);
    }

    public function passUserCapabilitiesDataProvider(): array
    {
        $getMockCapability = function (bool $shouldPass) {
            $mock = $this->createMock(UserCapability::class);

            $mock
                ->method('currentUserCan')
                ->willReturn($shouldPass);

            return $mock;
        };

        return [
            'Passes with no capabilities' => [$this->getSimpleMockNotice('foo'), true],
            'Passes with valid capabilities' => [
                $this->getSimpleMockNotice('foo')->ifUserCan($getMockCapability(true)),
                true,
            ],
            'Fails with invalid capabilities' => [
                $this->getSimpleMockNotice('foo')->ifUserCan($getMockCapability(false)),
                false,
            ],
            'Passes if at least one capability is valid' => [
                $this->getSimpleMockNotice('foo')->ifUserCan(
                    $getMockCapability(false),
                    $getMockCapability(true),
                    $getMockCapability(false)
                ),
                true,
            ],
        ];
    }

    public function testShouldPassScreenConditionsWhenThereAreNoConditions(): void
    {
        $displayNoticesInAdmin = new DisplayNoticesInAdmin();
        $notice = $this->getSimpleMockNotice('foo');

        $this->expectOutputString('foo');
        $displayNoticesInAdmin($notice);
    }

    public function testShouldPassScreenConditionsWhenConditionIsValidRegex(): void
    {
        $_SERVER['REQUEST_URI'] = 'http://example.com/wp-admin/dashboard.php';

        $displayNoticesInAdmin = new DisplayNoticesInAdmin();
        $notice = $this->getSimpleMockNotice('foo')->on('~Dashboard~i'); // check regex flags, too

        $this->expectOutputString('foo');
        $displayNoticesInAdmin($notice);
    }

    public function testShouldPassScreenConditionsWhenConditionIsValidString(): void
    {
        $_SERVER['REQUEST_URI'] = 'http://example.com/wp-admin/dashboard.php';

        $displayNoticesInAdmin = new DisplayNoticesInAdmin();
        $notice = $this->getSimpleMockNotice('foo')->on('dashboard');

        $this->expectOutputString('foo');
        $displayNoticesInAdmin($notice);
    }

    public function testShouldPassScreenConditionsWhenConditionMatchesWPScreen(): void
    {
        $_SERVER['REQUEST_URI'] = 'http://example.com/wp-admin/dashboard.php';

        $this->set_fn_return('get_current_screen', (object)['base' => 'dashboard']);

        // mock get_current_screen() global function
        $displayNoticesInAdmin = new DisplayNoticesInAdmin();
        $notice = $this->getSimpleMockNotice('foo')->on(['base' => 'dashboard']);

        $this->expectOutputString('foo');
        $displayNoticesInAdmin($notice);
    }

    /**
     * Produces a simple mock with predictable output.
     *
     * @since 1.0.0
     */
    private function getSimpleMockNotice($output): AdminNotice
    {
        return (new AdminNotice('test_id', $output))
            ->withoutWrapper()
            ->withoutAutoParagraph();
    }
}
