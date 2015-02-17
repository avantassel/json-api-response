<?php namespace Neomerx\JsonApi\Tests\Unit;

/**
 * Copyright 2015 info@neomerx.com (www.neomerx.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use \Mockery;
use \ReflectionClass;
use \Neomerx\JsonApi\Schema\ProviderBase;

class ProviderBaseTest extends BaseUnitTestCase
{
    /**
     * @var ProviderBase
     */
    private $provider;

    /**
     * @var ReflectionClass
     */
    private $reflector;

    public function setUp()
    {
        $this->provider = $this->getMockForAbstractClass(ProviderBase::class, ['id', 'schema://address', 'type']);
        $this->reflector = new ReflectionClass(ProviderBase::class);
    }

    public function testProtectedGetters()
    {
        $method = $this->reflector->getMethod('getIdentityProperty');
        $method->setAccessible(true);
        $this->assertEquals('id', $method->invoke($this->provider));

        $method = $this->reflector->getMethod('getUrl');
        $method->setAccessible(true);
        $this->assertEquals('schema://address', $method->invoke($this->provider));

        $method = $this->reflector->getMethod('getBaseUrl');
        $method->setAccessible(true);
        $this->assertEquals('', $method->invoke($this->provider));

        $method = $this->reflector->getMethod('getUrlTemplates');
        $method->setAccessible(true);
        $this->assertEquals([], $method->invoke($this->provider));
    }

    public function testComposeUrl()
    {
        $method = $this->reflector->getMethod('composeUrl');
        $method->setAccessible(true);

        $this->assertEquals('baseUrl/tailUrl', $method->invoke($this->provider, 'baseUrl', 'tailUrl'));
        $this->assertEquals('baseUrl/tailUrl', $method->invoke($this->provider, 'baseUrl/', 'tailUrl'));
        $this->assertEquals('baseUrl/tailUrl', $method->invoke($this->provider, 'baseUrl', '/tailUrl'));
        $this->assertEquals('baseUrl/tailUrl', $method->invoke($this->provider, 'baseUrl/', '/tailUrl'));
    }

    public function testGetUrls()
    {
        $method = $this->reflector->getMethod('setUrlTemplates');
        $method->setAccessible(true);
        $method->invoke($this->provider, [
            'type1' => 'url1',
            'type2' => ['url2', 'propertyName'],
        ]);

        $expected = [
            'type1' => ['url1', 'type1'],
            'type2' => ['url2', 'propertyName'],
        ];

        $this->assertEquals($expected, $this->provider->getUrls());
    }
}
