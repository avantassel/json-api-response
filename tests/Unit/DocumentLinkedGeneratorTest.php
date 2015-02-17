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
use \Neomerx\JsonApi\Tests\Data\Post;
use \Neomerx\JsonApi\Tests\Data\Author;
use \Neomerx\JsonApi\Tests\Data\AuthorProvider;
use \Neomerx\JsonApi\Contracts\SettingsInterface;
use \Neomerx\JsonApi\Generators\DocumentLinkedGenerator;

class DocumentLinkedGeneratorTest extends BaseUnitTestCase
{
    public function testAdd()
    {
        $post         = $this->posts[0];
        $settingsMock = Mockery::mock(SettingsInterface::class);

        $settingsMock->shouldReceive('linked')->once()->with(Mockery::type(Post::class))->andReturnSelf();
        $settingsMock->shouldReceive('isVisible')->once()->withNoArgs()->andReturn(true);
        $settingsMock->shouldReceive('linkedResource')->once()
            ->withArgs([Mockery::type(Post::class), Mockery::type(Author::class)])->andReturnSelf();

        /** @noinspection PhpParamsInspection */
        $generator = new DocumentLinkedGenerator($post, $settingsMock);
        $this->assertCount(0, $generator->getAdded());
        $generator->add($post->author, new AuthorProvider());
        $this->assertCount(1, $generator->getAdded());
    }

    public function testAddArray()
    {
        $post         = $this->posts[0];
        $settingsMock = Mockery::mock(SettingsInterface::class);

        $settingsMock->shouldReceive('linked')->once()->with(Mockery::type(Post::class))->andReturnSelf();
        $settingsMock->shouldReceive('isVisible')->once()->withNoArgs()->andReturn(true);
        $settingsMock->shouldReceive('linkedResource')->once()
            ->withArgs([Mockery::type(Post::class), Mockery::type(Author::class)])->andReturnSelf();

        /** @noinspection PhpParamsInspection */
        $generator = new DocumentLinkedGenerator($post, $settingsMock);
        $this->assertCount(0, $generator->getAdded());
        $generator->addArray([$post->author], new AuthorProvider());
        $this->assertCount(1, $generator->getAdded());
    }

    public function testCheckIgnoreAddingIfLinkedNotVisible()
    {
        $post         = $this->posts[0];
        $settingsMock = Mockery::mock(SettingsInterface::class);

        $settingsMock->shouldReceive('linked')->once()->with(Mockery::type(Post::class))->andReturnSelf();
        $settingsMock->shouldReceive('isVisible')->once()->withNoArgs()->andReturn(false);

        /** @noinspection PhpParamsInspection */
        $generator = new DocumentLinkedGenerator($post, $settingsMock);

        $this->assertCount(0, $generator->getAdded());

        $generator->add($post->author, new AuthorProvider());
        $this->assertCount(0, $generator->getAdded());

        $generator->addArray([$post->author], new AuthorProvider());
        $this->assertCount(0, $generator->getAdded());

        $this->assertNull($generator->generate());
    }

    public function testGenerate()
    {
        $post         = $this->posts[0];
        $settingsMock = Mockery::mock(SettingsInterface::class);

        $settingsMock->shouldReceive('linked')->once()->with(Mockery::type(Post::class))->andReturnSelf();
        $settingsMock->shouldReceive('isVisible')->once()->withNoArgs()->andReturn(true);
        $settingsMock->shouldReceive('linkedResource')->once()
            ->withArgs([Mockery::type(Post::class), Mockery::type(Author::class)])->andReturnSelf();
        $settingsMock->shouldReceive('hasAttributes')->once()->withNoArgs()->andReturn(false);
        $settingsMock->shouldReceive('hasReference')->once()->withNoArgs()->andReturn(false);
        $settingsMock->shouldReceive('hasType')->once()->withNoArgs()->andReturn(false);

        /** @noinspection PhpParamsInspection */
        $generator = new DocumentLinkedGenerator($post, $settingsMock);
        $generator->add($post->author, new AuthorProvider());

        $result = $generator->generate();
        $expected = (object)[
            'people' => [
                (object)['id' => '9'],
            ]
        ];

        $this->assertEquals($expected, $result);
    }
}
