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
use \Neomerx\JsonApi\Tests\Data\PostProvider;
use \Neomerx\JsonApi\Contracts\SettingsInterface;
use \Neomerx\JsonApi\Generators\LinkToCollectionGenerator;

class LinkToCollectionGeneratorTest extends BaseUnitTestCase
{
    public function testGenerateAsIdentity()
    {
        $generator = new LinkToCollectionGenerator(SettingsInterface::TYPE_IDENTITY, true, true, true);

        $result   = $generator->generate($this->posts, new PostProvider());
        $expected = ['1', '2'];

        $this->assertEquals($expected, $result);

    }

    public function testGenerateAsObject()
    {
        $generator = new LinkToCollectionGenerator(SettingsInterface::TYPE_OBJECT, true, true, true);

        $result   = $generator->generate($this->posts, new PostProvider());
        $expected = [
            (object)[
                'id'    => '1',
                'title' => 'Rails is Omakase',
                'body'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla.',
                'href'  => 'http://example.com/posts/1',
                'type'  => 'posts',
            ],
            (object)[
                'id'    => '2',
                'title' => 'Lorem ipsum dolor',
                'body'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla.',
                'href'  => 'http://example.com/posts/2',
                'type'  => 'posts',
            ],
        ];

        $this->assertEquals($expected, $result);

    }

    public function testGenerateAsCombined()
    {
        $generator = new LinkToCollectionGenerator(SettingsInterface::TYPE_COMBINED, true, true, true);

        $result   = $generator->generate($this->posts, new PostProvider());
        $expected = (object)[
            'ids'   => ['1', '2'],
            'href'  => 'http://example.com/posts/1,2',
            'type'  => 'posts',
        ];

        $this->assertEquals($expected, $result);

    }
}
