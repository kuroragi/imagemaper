<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TestFixture\Event\RiskyBecauseDependencyOnLargerTest;

use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\TestCase;

#[Large]
final class LargeTest extends TestCase
{
    public function testOne(): void
    {
        $this->assertTrue(true);
    }
}
