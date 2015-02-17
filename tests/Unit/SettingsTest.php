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

use \Neomerx\JsonApi\Tests\Data\Post;
use \Neomerx\JsonApi\Settings\Settings;
use \Neomerx\JsonApi\Tests\Data\Author;
use \Neomerx\JsonApi\Tests\Data\PostSchema;

class SettingsTest extends BaseUnitTestCase
{
    public function testAlterDefaultSettings()
    {
        $defaults = Settings::defaults();

        $alterLinked = [
            Post::class => [Settings::SET_VISIBLE => true],
        ];
        $alterLinkedResource = [
            Post::class => [
                Author::class => [Settings::SET_HAS_TYPE => true],
            ],
        ];

        $updatedDefaults = new Settings(
            $defaults->getDefaultMeta(),
            $defaults->getDefaultLinks(),
            $defaults->getDefaultLinked(),
            $defaults->getDefaultLinkedResource(),
            $defaults->getDefaultDataSingle(),
            $defaults->getDefaultDataCollection(),
            $defaults->getDefaultDataSingleLinkSingle(),
            $defaults->getDefaultDataCollectionLinkSingle(),
            $defaults->getDefaultDataSingleLinkCollection(),
            $defaults->getDefaultDataCollectionLinkCollection(),
            [],
            [],
            $alterLinked,
            $alterLinkedResource
        );

        $post = $this->posts[0];

        $this->assertFalse($defaults->linked($post)->isVisible());
        $this->assertTrue($updatedDefaults->linked($post)->isVisible());

        $this->assertFalse($defaults->linkedResource($post, $post->{PostSchema::PROPERTY_AUTHOR})->hasType());
        $this->assertTrue($updatedDefaults->linkedResource($post, $post->{PostSchema::PROPERTY_AUTHOR})->hasType());
    }
}
