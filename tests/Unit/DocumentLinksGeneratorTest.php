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
use \Neomerx\JsonApi\Settings\Settings;
use \Neomerx\JsonApi\Tests\Data\PostProvider;
use \Neomerx\JsonApi\Tests\Data\AuthorProvider;
use \Neomerx\JsonApi\Generators\DocumentLinksGenerator;

class DocumentLinksGeneratorTest extends BaseUnitTestCase
{
    public function testGenerateAddedLinkAsObject()
    {
        $post      = $this->posts[0];
        $settings  = Settings::defaults();
        $settings->getDefaultLinks()->setVisible(true);
        $settings->getDefaultLinks()->setHasType(true);
        $settings->getDefaultLinks()->setRepresentationType(Settings::TYPE_OBJECT);
        $generator = new DocumentLinksGenerator($post, new PostProvider(), $settings);

        $generator->addLink(['posts', 'someProperty'], 'schema://url', 'linkType');

        $result   = $generator->generate();
        $expected = (object)[
            'posts.someProperty' => (object)[
                'href' => 'schema://url',
                'type' => 'linkType',
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGenerateAddedLinkAsIdentity()
    {
        $post      = $this->posts[0];
        $settings  = Settings::defaults();
        $settings->getDefaultLinks()->setVisible(true);
        $settings->getDefaultLinks()->setHasType(true);
        $settings->getDefaultLinks()->setRepresentationType(Settings::TYPE_IDENTITY);
        $generator = new DocumentLinksGenerator($post, new PostProvider(), $settings);

        $generator->addLink(['posts', 'someProperty'], 'schema://url', 'linkType');

        $result   = $generator->generate();
        $expected = (object)[
            'posts.someProperty' => 'schema://url',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGenerateAddByPropertyAsObject()
    {
        $post      = $this->posts[0];
        $settings  = Settings::defaults();
        $settings->getDefaultLinks()->setVisible(true);
        $settings->getDefaultLinks()->setHasType(true);
        $settings->getDefaultLinks()->setRepresentationType(Settings::TYPE_OBJECT);
        $generator = new DocumentLinksGenerator($post, new PostProvider(), $settings);

        $generator->addByProperty('writer', new AuthorProvider());

        $result   = $generator->generate();
        $expected = (object)[
            'posts.writer' => (object)[
                'href' => 'http://example.com/people/{posts.writer}',
                'type' => 'people',
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGenerateAddByPropertyAsIdentity()
    {
        $post      = $this->posts[0];
        $settings  = Settings::defaults();
        $settings->getDefaultLinks()->setVisible(true);
        $settings->getDefaultLinks()->setHasType(true);
        $settings->getDefaultLinks()->setRepresentationType(Settings::TYPE_IDENTITY);
        $generator = new DocumentLinksGenerator($post, new PostProvider(), $settings);

        $generator->addByProperty('writer', new AuthorProvider());

        $result   = $generator->generate();
        $expected = (object)[
            'posts.writer' => 'http://example.com/people/{posts.writer}',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGenerateWhenLinksHidden()
    {
        $post      = $this->posts[0];
        $settings  = Settings::defaults();
        $settings->getDefaultLinks()->setVisible(false);
        $settings->getDefaultLinks()->setRepresentationType(Settings::TYPE_IDENTITY);
        $generator = new DocumentLinksGenerator($post, new PostProvider(), $settings);

        $generator->addByProperty('writer', new AuthorProvider());
        $generator->addLink(['posts', 'someProperty'], 'schema://url', 'linkType');

        $result   = $generator->generate();
        $expected = null;

        $this->assertEquals($expected, $result);
    }
}
