<?php
/**
 * Copyright (C) 2015 by Dan Revel <dan@nopolabs.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Functional\Tests;

use ArrayIterator;
use Functional\Exceptions\InvalidArgumentException;
use function Functional\flat_map;

class FlatMapTest extends AbstractTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->list = ['v1', 'v2', 'v3'];
        $this->listIterator = new ArrayIterator($this->list);
        $this->hash = ['k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'];
        $this->hashIterator = new ArrayIterator($this->hash);
    }

    public function test()
    {
        $fn = function($v, $k, $collection) {
            InvalidArgumentException::assertCollection($collection, __FUNCTION__, 3);
            if ($v === 'v3') {
                return []; // flat_map will drop an empty array
            }
            $nestedArray = str_split($v);
            return [$k, $v, $nestedArray]; // flat_map will flatten one level of nesting
        };
        $this->assertEquals(['0','v1', ['v','1'],'1','v2', ['v','2']], flat_map($this->list, $fn));
        $this->assertEquals(['0','v1', ['v','1'],'1','v2', ['v','2']], flat_map($this->listIterator, $fn));
        $this->assertEquals(['k1','v1', ['v','1'],'k2','v2', ['v','2']], flat_map($this->hash, $fn));
        $this->assertEquals(['k1','v1', ['v','1'],'k2','v2', ['v','2']], flat_map($this->hashIterator, $fn));
    }

    public function testPassNoCollection()
    {
        $this->expectArgumentError('Functional\flat_map() expects parameter 1 to be array or instance of Traversable');
        flat_map('invalidCollection', 'strlen');
    }

    public function testPassNonCallable()
    {
        $this->expectArgumentError("Argument 2 passed to Functional\\flat_map() must be callable");
        flat_map($this->list, 'undefinedFunction');
    }
}
