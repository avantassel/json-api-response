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
use \Mockery\MockInterface;
use \Neomerx\JsonApi\Tests\Data\Post;
use \Neomerx\JsonApi\Tests\Data\Author;
use \Neomerx\JsonApi\Tests\Data\Comment;
use \Neomerx\JsonApi\Tests\Data\PostSchema;
use \Neomerx\JsonApi\Tests\Data\AuthorSchema;
use \Neomerx\JsonApi\Tests\Data\PostProvider;
use \Neomerx\JsonApi\Tests\Data\CommentSchema;
use \Neomerx\JsonApi\Tests\Data\AuthorProvider;
use \Neomerx\JsonApi\Tests\Data\CommentProvider;
use \Neomerx\JsonApi\Contracts\SettingsInterface;
use \Neomerx\JsonApi\Contracts\ContainerInterface;
use \Neomerx\JsonApi\Generators\ResourceGenerator;
use \Neomerx\JsonApi\Generators\DocumentLinksGenerator;
use \Neomerx\JsonApi\Generators\DocumentLinkedGenerator;
use \Neomerx\JsonApi\Settings\DataLinkSingleRepresentation;
use \Neomerx\JsonApi\Settings\DataLinkCollectionRepresentation;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ResourceGeneratorTest extends BaseUnitTestCase
{
    /**
     * @var ResourceGenerator
     */
    private $generator;

    /**
     * @var Post
     */
    private $post;

    /**
     * @var MockInterface
     */
    private $settingsMock;

    /**
     * @var MockInterface
     */
    private $containerMock;

    /**
     * @var MockInterface
     */
    private $linkedMock;

    /**
     * @var MockInterface
     */
    private $linksMock;

    public function setUp()
    {
        parent::setUp();

        $this->settingsMock  = Mockery::mock(SettingsInterface::class);
        $this->containerMock = Mockery::mock(ContainerInterface::class);
        $this->linkedMock    = Mockery::mock(DocumentLinkedGenerator::class);
        $this->linksMock     = Mockery::mock(DocumentLinksGenerator::class);

        $this->linkedMock->shouldReceive('add')->zeroOrMoreTimes()->withAnyArgs()->andReturnUndefined();
        $this->linkedMock->shouldReceive('addArray')->zeroOrMoreTimes()->withAnyArgs()->andReturnUndefined();

        $this->linksMock->shouldReceive('addByProperty')->zeroOrMoreTimes()->withAnyArgs()->andReturnUndefined();
        $this->linksMock->shouldReceive('addLink')->zeroOrMoreTimes()->withAnyArgs()->andReturnUndefined();

        /** @noinspection PhpParamsInspection */
        $this->generator = new ResourceGenerator(
            SettingsInterface::TYPE_OBJECT,
            true,
            true,
            true,
            true,
            true,
            $this->settingsMock,
            $this->containerMock,
            $this->linkedMock,
            $this->linksMock
        );

        $this->post = PostSchema::create(
            '1',
            'Title',
            AuthorSchema::create('2', 'John Dow'),
            [CommentSchema::create('3'), CommentSchema::create('4')],
            'Body'
        );
    }

    public function testGenerateAsObject()
    {
        $expected = (object)[
            'id'    => '1',
            'title' => 'Title',
            'body'  => 'Body',
            'href'  => 'http://example.com/posts/1',
            'type'  => 'posts',
            'links' => (object)[
                'author'   => '2',
                'comments' => ['3', '4']
            ]
        ];

        $this->prepareMockForAuthor();
        $this->prepareMockForComments();

        $result = $this->generator->generate($this->post, new PostProvider());
        $this->assertEquals($expected, $result);
    }

    public function testGenerateAsObjectWithNullAuthor()
    {
        $this->post->author = null;

        $expected = (object)[
            'id'    => '1',
            'title' => 'Title',
            'body'  => 'Body',
            'href'  => 'http://example.com/posts/1',
            'type'  => 'posts',
            'links' => (object)[
                'author'   => null,
                'comments' => ['3', '4']
            ]
        ];

        $this->prepareMockForComments();

        $result = $this->generator->generate($this->post, new PostProvider());
        $this->assertEquals($expected, $result);
    }

    public function testGenerateAsObjectWithEmptyComments()
    {
        $this->post->comments = [];

        $expected = (object)[
            'id'    => '1',
            'title' => 'Title',
            'body'  => 'Body',
            'href'  => 'http://example.com/posts/1',
            'type'  => 'posts',
            'links' => (object)[
                'author'   => '2',
                'comments' => []
            ]
        ];

        $this->prepareMockForAuthor();

        $result = $this->generator->generate($this->post, new PostProvider());
        $this->assertEquals($expected, $result);
    }

    public function testGenerateAsObjectWithNullComments()
    {
        $this->post->comments = null;

        $expected = (object)[
            'id'    => '1',
            'title' => 'Title',
            'body'  => 'Body',
            'href'  => 'http://example.com/posts/1',
            'type'  => 'posts',
            'links' => (object)[
                'author'   => '2',
                'comments' => null
            ]
        ];

        $this->prepareMockForAuthor();

        $result = $this->generator->generate($this->post, new PostProvider());
        $this->assertEquals($expected, $result);
    }

    public function testGenerateAsObjectNoLinks()
    {
        /** @noinspection PhpParamsInspection */
        $this->generator = new ResourceGenerator(
            SettingsInterface::TYPE_OBJECT,
            true,
            false, // <- links
            true,
            true,
            false,
            $this->settingsMock,
            $this->containerMock,
            $this->linkedMock,
            $this->linksMock
        );

        $expected = (object)[
            'id'    => '1',
            'title' => 'Title',
            'body'  => 'Body',
            'href'  => 'http://example.com/posts/1',
            'type'  => 'posts'
        ];

        $result = $this->generator->generate($this->post, new PostProvider());
        $this->assertEquals($expected, $result);
    }

    private function prepareMockForAuthor()
    {
        $this->containerMock->shouldReceive('getProvider')
            ->with(Mockery::type(Author::class))->once()->andReturn(new AuthorProvider);

        $authorSettings = new DataLinkSingleRepresentation();
        $authorSettings->setRepresentationType(SettingsInterface::TYPE_IDENTITY);
        $authorSettings->setHasAttributes(true);
        $authorSettings->setHasReference(true);
        $authorSettings->setHasType(true);
        $this->settingsMock->shouldReceive('dataSingleLinkSingle')->once()
            ->withArgs([Mockery::type(Post::class), Mockery::type(Author::class)])->andReturn($authorSettings);
    }

    private function prepareMockForComments()
    {
        $this->containerMock->shouldReceive('getProvider')
            ->with(Mockery::type(Comment::class))->once()->andReturn(new CommentProvider);

        $commentsSettings = new DataLinkCollectionRepresentation();
        $commentsSettings->setRepresentationType(SettingsInterface::TYPE_IDENTITY);
        $commentsSettings->setHasAttributes(true);
        $commentsSettings->setHasReference(true);
        $commentsSettings->setHasType(true);
        $this->settingsMock->shouldReceive('dataSingleLinkCollection')->once()
            ->withArgs([Mockery::type(Post::class), Mockery::type(Comment::class)])->andReturn($commentsSettings);
    }
}
