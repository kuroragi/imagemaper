<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-timer.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Timer;

use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ResourceUsageFormatter::class)]
#[UsesClass(Duration::class)]
#[UsesClass(Timer::class)]
final class ResourceUsageFormatterTest extends TestCase
{
    private ResourceUsageFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new ResourceUsageFormatter;
    }

    public function testCanFormatResourceUsage(): void
    {
        $this->assertStringMatchesFormat(
            'Time: 01:01, Memory: %s',
            $this->formatter->resourceUsage(
                Duration::fromMicroseconds(61000000)
            )
        );
    }

    public function testCanFormatResourceUsageSinceStartOfRequest(): void
    {
        $this->assertStringMatchesFormat(
            'Time: %s, Memory: %s',
            $this->formatter->resourceUsageSinceStartOfRequest()
        );
    }

    #[BackupGlobals(true)]
    #[TestDox('Cannot format resource usage since start of request when $_SERVER[\'REQUEST_TIME_FLOAT\'] is not available')]
    public function testCannotFormatResourceUsageSinceStartOfRequestWhenRequestTimeFloatIsNotAvailable(): void
    {
        unset($_SERVER['REQUEST_TIME_FLOAT']);

        $this->expectException(TimeSinceStartOfRequestNotAvailableException::class);

        $this->formatter->resourceUsageSinceStartOfRequest();
    }

    #[BackupGlobals(true)]
    #[TestDox('Cannot format resource usage since start of request when $_SERVER[\'REQUEST_TIME_FLOAT\'] is not valid')]
    public function testCannotFormatResourceUsageSinceStartOfRequestWhenRequestTimeFloatIsNotValid(): void
    {
        $_SERVER['REQUEST_TIME_FLOAT'] = 'string';

        $this->expectException(TimeSinceStartOfRequestNotAvailableException::class);

        $this->formatter->resourceUsageSinceStartOfRequest();
    }
}
