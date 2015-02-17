<?php namespace Neomerx\JsonApi\Tests\Integration;

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

use \Neomerx\JsonApi\Encoder;
use \Neomerx\JsonApi\Tests\Data\Post;
use \Neomerx\JsonApi\Settings\Settings;
use \Neomerx\JsonApi\Tests\Data\Author;
use \Neomerx\JsonApi\Tests\Data\Comment;
use \Neomerx\JsonApi\Tests\Data\PostProvider;
use \Neomerx\JsonApi\Schema\ProviderContainer;
use \Neomerx\JsonApi\Tests\Data\AuthorProvider;
use \Neomerx\JsonApi\Tests\Data\CommentProvider;
use \Neomerx\JsonApi\Contracts\SettingsInterface;
use \Neomerx\JsonApi\Tests\Data\PostProviderNoUrlTemplates;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class EncoderTest extends BaseIntegrationTestCase
{
    private $providerContainer;

    public function setUp()
    {
        parent::setUp();

        $this->providerContainer = new ProviderContainer([
            Author::class  => AuthorProvider::class,
            Comment::class => CommentProvider::class,
            Post::class    => PostProvider::class,
        ]);
    }

    public function testEncodeEmptyInput()
    {
        $settings = Settings::defaults();

        $encoder = new Encoder($settings, $this->providerContainer);
        $this->assertEquals('{"data":[]}', $encoder->encode([]));
        $this->assertEquals('{"data":null}', $encoder->encode(null));
    }

    public function testEncodeWithMeta()
    {
        $settings = Settings::defaults();
        $settings->getDefaultMeta()->setVisible(true);
        $settings->getDefaultDataSingle()->setRepresentationType(SettingsInterface::TYPE_IDENTITY);

        $meta = (object)['property' => 'value'];

        $expected = <<<EOT
        {
            "meta" : {
                "property" : "value"
            },
            "posts" : "1"
        }
EOT;

        // remove formatting from 'expected'
        $expected = json_encode(json_decode($expected));

        $encoder = new Encoder($settings, $this->providerContainer);
        $this->assertEquals($expected, $encoder->encode($this->posts[0], $meta));
    }

    /**
     * @link http://jsonapi.org/format/#document-structure-resource-objects
     */
    public function testEncodeResourceObject()
    {
        $settings = Settings::defaults();
        $settings->getDefaultLinked()->setVisible(false);
        $settings->getDefaultDataSingle()->setHasLinks(false);
        $settings->getDefaultDataSingle()->setHasReference(false);

        $expected = <<<EOT
        {
            "posts" : {
                "id"    : "1",
                "title" : "Rails is Omakase",
                "body"  : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla."
            }
        }
EOT;

        // remove formatting from 'expected'
        $expected = json_encode(json_decode($expected));

        $encoder = new Encoder($settings, $this->providerContainer);
        $this->assertEquals($expected, $encoder->encode($this->posts[0]));
    }

    /**
     * @link http://jsonapi.org/format/#document-structure-resource-collection-representations
     */
    public function testEncodeCombinedCollection()
    {
        $settings = Settings::defaults();
        $settings->getDefaultDataCollection()->setRepresentationType(SettingsInterface::TYPE_COMBINED);

        $expected = <<<EOT
        {
            "comments" : {
                "href" : "http://example.com/comments/1,2",
                "ids"  : [ "1", "2" ],
                "type" : "comments"
            }
        }
EOT;

        // remove formatting from 'expected'
        $expected = json_encode(json_decode($expected));

        $encoder = new Encoder($settings, $this->providerContainer);
        $this->assertEquals($expected, $encoder->encode($this->comments));
    }

    /**
     * @link http://jsonapi.org/format/#document-structure-resource-urls
     */
    public function testEncodeCollectionWithReferencesInsideResources()
    {
        $settings = Settings::defaults();
        $settings->getDefaultDataCollection()->setHasLinks(false);

        $expected = <<<EOT
        {
            "comments" : [
                {
                    "id"   : "1",
                    "body" : "Mmmmmakase",
                    "href" : "http://example.com/comments/1"
                }, {
                    "id"   : "2",
                    "body" : "I prefer unagi",
                    "href" : "http://example.com/comments/2"
                }
            ]
        }
EOT;

        // remove formatting from 'expected'
        $expected = json_encode(json_decode($expected));

        $encoder = new Encoder($settings, $this->providerContainer);
        $this->assertEquals($expected, $encoder->encode($this->comments));
    }

    /**
     * @link http://jsonapi.org/format/#document-structure-resource-relationships
     */
    public function testEncodePostWithIdentityLinks()
    {
        $settings = Settings::defaults();
        $settings->getDefaultDataSingle()->setHasReference(false);
        $settings->getDefaultDataSingleLinkSingle()->setRepresentationType(SettingsInterface::TYPE_IDENTITY);
        $settings->getDefaultDataSingleLinkCollection()->setRepresentationType(SettingsInterface::TYPE_IDENTITY);

        $expected = <<<EOT
        {

            "posts" : {
                "id"    : "1",
                "title" : "Rails is Omakase",
                "body"  : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla.",
                "links" : {
                    "author"   : "9",
                    "comments" : [ "5", "12", "17", "20" ]
                }
            }
        }
EOT;

        // remove formatting from 'expected'
        $expected = json_encode(json_decode($expected));

        $encoder = new Encoder($settings, $this->providerContainer);
        $this->assertEquals($expected, $encoder->encode($this->posts[0]));
    }

    /**
     * @link http://jsonapi.org/format/#document-structure-resource-relationships-to-one
     */
    public function testEncodePostWithObjectLinks()
    {
        $settings = Settings::defaults();
        $settings->getDefaultDataSingle()->setHasReference(false);
        $settings->getDefaultDataSingleLinkSingle()->setRepresentationType(SettingsInterface::TYPE_OBJECT);
        $settings->getDefaultDataSingleLinkSingle()->setHasType(true);
        $settings->getDefaultDataSingleLinkSingle()->setHasReference(true);
        $settings->getDefaultDataSingleLinkCollection()->setRepresentationType(SettingsInterface::TYPE_IDENTITY);

        $expected = <<<EOT
        {

            "posts" : {
                "id"    : "1",
                "title" : "Rails is Omakase",
                "body"  : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla.",
                "links" : {
                    "author"  : {
                        "id"   : "9",
                        "name" : "John Author",
                        "href" : "http://example.com/people/9",
                        "type" : "people"
                    },
                    "comments" : [ "5", "12", "17", "20" ]
                }
            }
        }
EOT;

        // remove formatting from 'expected'
        $expected = json_encode(json_decode($expected));

        $encoder = new Encoder($settings, $this->providerContainer);
        $this->assertEquals($expected, $encoder->encode($this->posts[0]));
    }

    /**
     * @link http://jsonapi.org/format/#document-structure-resource-relationships-to-one
     */
    public function testEncodePostWithObjectLinksWithNullAuthor()
    {
        $settings = Settings::defaults();
        $settings->getDefaultDataSingle()->setHasReference(false);
        $settings->getDefaultDataSingleLinkSingle()->setRepresentationType(SettingsInterface::TYPE_OBJECT);
        $settings->getDefaultDataSingleLinkSingle()->setHasType(true);
        $settings->getDefaultDataSingleLinkSingle()->setHasReference(true);
        $settings->getDefaultDataSingleLinkCollection()->setRepresentationType(SettingsInterface::TYPE_IDENTITY);

        $expected = <<<EOT
        {

            "posts" : {
                "id"    : "1",
                "title" : "Rails is Omakase",
                "body"  : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla.",
                "links" : {
                    "author"   : null,
                    "comments" : [ "5", "12", "17", "20" ]
                }
            }
        }
EOT;

        // remove formatting from 'expected'
        $expected = json_encode(json_decode($expected));

        $encoder = new Encoder($settings, $this->providerContainer);
        $post = $this->posts[0];
        $post->author = null;
        $this->assertEquals($expected, $encoder->encode($post));
    }

    /**
     * @link http://jsonapi.org/format/#document-structure-resource-relationships-to-many
     */
    public function testEncodePostLinkCollectionCombined()
    {
        $settings = Settings::defaults();
        $settings->getDefaultDataSingle()->setHasReference(false);
        $settings->getDefaultDataSingleLinkSingle()->setRepresentationType(SettingsInterface::TYPE_IDENTITY);
        $settings->getDefaultDataSingleLinkSingle()->setHasType(true);
        $settings->getDefaultDataSingleLinkSingle()->setHasReference(true);
        $settings->getDefaultDataSingleLinkCollection()->setRepresentationType(SettingsInterface::TYPE_COMBINED);

        $expected = <<<EOT
        {

            "posts" : {
                "id"    : "1",
                "title" : "Rails is Omakase",
                "body"  : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla.",
                "links" : {
                    "author"   : "9",
                    "comments" : {
                        "href" : "http://example.com/comments/5,12,17,20",
                        "ids"  : [ "5", "12", "17", "20" ],
                        "type" : "comments"
                    }
                }
            }
        }
EOT;
        // remove formatting from 'expected'
        $expected = json_encode(json_decode($expected));

        $encoder = new Encoder($settings, $this->providerContainer);
        $post = $this->posts[0];
        $this->assertEquals($expected, $encoder->encode($post));
    }

    /**
     * @link http://jsonapi.org/format/#document-structure-resource-relationships-to-many
     */
    public function testEncodePostLinkBlankCollection()
    {
        $settings = Settings::defaults();
        $settings->getDefaultDataSingle()->setHasReference(false);
        $settings->getDefaultDataSingleLinkSingle()->setRepresentationType(SettingsInterface::TYPE_IDENTITY);
        $settings->getDefaultDataSingleLinkSingle()->setHasType(true);
        $settings->getDefaultDataSingleLinkSingle()->setHasReference(true);
        $settings->getDefaultDataSingleLinkCollection()->setRepresentationType(SettingsInterface::TYPE_OBJECT);

        $expected = <<<EOT
        {

            "posts" : {
                "id"    : "1",
                "title" : "Rails is Omakase",
                "body"  : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla.",
                "links" : {
                    "author"   : "9",
                    "comments" : []
                }
            }
        }
EOT;
        // remove formatting from 'expected'
        $expected = json_encode(json_decode($expected));

        $encoder = new Encoder($settings, $this->providerContainer);
        $post = $this->posts[0];
        $post->comments = [];
        $this->assertEquals($expected, $encoder->encode($post));
    }

    /**
     * @link http://jsonapi.org/format/#document-structure-url-templates
     */
    public function testEncodePostCollectionWithUrlTemplatesNoLinks()
    {
        $settings = Settings::defaults();
        $settings->getDefaultLinks()->setVisible(true);
        $settings->getDefaultDataCollection()->setHasLinks(false);
        $settings->getDefaultDataCollection()->setHasReference(false);

        $expected = <<<EOT
        {
            "links" : {
                "posts.comments" : "http://example.com/comments?posts={posts.id}"
            },
            "posts" : [
                {
                    "id"    : "1",
                    "title" : "Rails is Omakase",
                    "body"  : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla."
                },
                {
                    "id"    : "2",
                    "title" : "Lorem ipsum dolor",
                    "body"  : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla."
                }
            ]
        }
EOT;
        // remove formatting from 'expected'
        $expected = json_encode(json_decode($expected));

        $encoder = new Encoder($settings, $this->providerContainer);
        $this->assertEquals($expected, $encoder->encode($this->posts));
    }

    /**
     * @link http://jsonapi.org/format/#document-structure-url-templates
     */
    public function testEncodePostCollectionWithUrlTemplatesAndMainRefAndNoLinks()
    {
        $settings = Settings::defaults();
        $settings->getDefaultLinks()->setVisible(true);
        $settings->getDefaultDataCollection()->setHasLinks(false);
        $settings->getDefaultDataCollection()->setHasReference(false);

        $settings->getDefaultDataCollectionLinkSingle()->setHasReference(true);

        $expected = <<<EOT
        {
            "links" : {
                "posts.author"   : "http://example.com/people/{posts.author}",
                "posts.comments" : "http://example.com/comments?posts={posts.id}"
            },
            "posts" : [
                {
                    "id"    : "1",
                    "title" : "Rails is Omakase",
                    "body"  : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla."
                },
                {
                    "id"    : "2",
                    "title" : "Lorem ipsum dolor",
                    "body"  : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla."
                }
            ]
        }
EOT;
        // remove formatting from 'expected'
        $expected = json_encode(json_decode($expected));

        $encoder = new Encoder($settings, $this->providerContainer);
        $this->assertEquals($expected, $encoder->encode($this->posts));
    }

    /**
     * @link http://jsonapi.org/format/#document-structure-url-templates
     */
    public function testEncodePostSingleWithUrlTemplatesNoLinks()
    {
        $settings = Settings::defaults();
        $settings->getDefaultLinks()->setVisible(true);
        $settings->getDefaultDataSingle()->setHasLinks(false);
        $settings->getDefaultDataSingle()->setHasReference(false);

        $expected = <<<EOT
        {
            "links" : {
                "posts.comments" : "http://example.com/comments?posts={posts.id}"
            },
            "posts" : {
                "id"    : "1",
                "title" : "Rails is Omakase",
                "body"  : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla."
            }

        }
EOT;
        // remove formatting from 'expected'
        $expected = json_encode(json_decode($expected));

        $encoder = new Encoder($settings, $this->providerContainer);
        $this->assertEquals($expected, $encoder->encode($this->posts[0]));
    }

    /**
     * @link http://jsonapi.org/format/#document-structure-url-templates
     */
    public function testEncodePostSingleWithUrlTemplatesAndMainRefAndNoLinks()
    {
        $settings = Settings::defaults();
        $settings->getDefaultLinks()->setVisible(true);
        $settings->getDefaultDataSingle()->setHasLinks(false);
        $settings->getDefaultDataSingle()->setHasReference(false);
        $settings->getDefaultDataSingleLinkSingle()->setHasReference(true);

        $expected = <<<EOT
        {
            "links": {
                "posts.author"   : "http://example.com/people/{posts.author}",
                "posts.comments" : "http://example.com/comments?posts={posts.id}"
            },
            "posts" : {
                "id"    : "1",
                "title" : "Rails is Omakase",
                "body"  : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla."
            }

        }
EOT;
        // remove formatting from 'expected'
        $expected = json_encode(json_decode($expected));

        $encoder = new Encoder($settings, $this->providerContainer);
        $this->assertEquals($expected, $encoder->encode($this->posts[0]));
    }

    /**
     * Test encode document links as objects
     */
    public function testEncodeDocumentLinksAsObjects()
    {
        $settings = Settings::defaults();
        $settings->getDefaultLinks()->setVisible(true);
        $settings->getDefaultLinks()->setHasType(true);
        $settings->getDefaultLinks()->setRepresentationType(Settings::TYPE_OBJECT);
        $settings->getDefaultDataSingle()->setHasLinks(false);
        $settings->getDefaultDataSingle()->setHasReference(false);
        $settings->getDefaultDataSingleLinkSingle()->setHasReference(true);

        $expected = <<<EOT
        {
            "links" : {
                "posts.author" : {
                    "href" : "http://example.com/people/{posts.author}",
                    "type" : "people"
                },
                "posts.comments" : {
                    "href" : "http://example.com/comments?posts={posts.id}",
                    "type" : "comments"
                }
            },
            "posts" : {
                "id"    : "1",
                "title" : "Rails is Omakase",
                "body"  : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla."
            }

        }
EOT;
        // remove formatting from 'expected'
        $expected = json_encode(json_decode($expected));

        $encoder = new Encoder($settings, $this->providerContainer);
        $this->assertEquals($expected, $encoder->encode($this->posts[0]));
    }

    /**
     * @link http://jsonapi.org/format/#document-structure-url-templates
     */
    public function testEncodePostCollectionNoUrlTemplatesWithLinks()
    {
        $settings = Settings::defaults();
        $settings->getDefaultLinks()->setVisible(true);
        $settings->getDefaultDataCollection()->setHasLinks(true);
        $settings->getDefaultDataCollection()->setHasReference(false);
        $settings->getDefaultDataCollectionLinkSingle()->setHasReference(true);
        $settings->getDefaultDataCollectionLinkCollection()->setHasReference(true);
        $settings->getDefaultDataCollectionLinkSingle()->setRepresentationType(Settings::TYPE_IDENTITY);
        $settings->getDefaultDataCollectionLinkCollection()->setRepresentationType(Settings::TYPE_IDENTITY);

        $expected = <<<EOT
        {
            "links" : {
                "posts.author"   : "http://example.com/people/{posts.author}",
                "posts.comments" : "http://example.com/comments/{posts.comments}"
            },
            "posts" : [
                {
                    "id"    : "1",
                    "title" : "Rails is Omakase",
                    "body"  : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla.",
                    "links" : {
                        "author"   : "9",
                        "comments" : [ "5", "12", "17", "20" ]
                    }
                }
            ]
        }
EOT;
        // remove formatting from 'expected'
        $expected = json_encode(json_decode($expected));

        $this->providerContainer = new ProviderContainer([
            Author::class  => AuthorProvider::class,
            Comment::class => CommentProvider::class,
            Post::class    => PostProviderNoUrlTemplates::class,
        ]);

        $encoder = new Encoder($settings, $this->providerContainer);
        $this->assertEquals($expected, $encoder->encode([$this->posts[0]]));
    }

    /**
     * @link http://jsonapi.org/format/#document-structure-compound-documents
     */
    public function testEncodePostCollectionWithLinked()
    {
        $settings = Settings::defaults();
        $settings->getDefaultLinks()->setVisible(true);
        $settings->getDefaultLinks()->setHasType(true);
        $settings->getDefaultLinks()->setRepresentationType(Settings::TYPE_OBJECT);
        $settings->getDefaultLinked()->setVisible(true);
        $settings->getDefaultLinkedResource()->setHasAttributes(true);
        $settings->getDefaultDataCollection()->setHasLinks(true);
        $settings->getDefaultDataCollection()->setHasReference(false);
        $settings->getDefaultDataCollectionLinkSingle()->setRepresentationType(Settings::TYPE_IDENTITY);
        $settings->getDefaultDataCollectionLinkSingle()->setHasReference(true);
        $settings->getDefaultDataCollectionLinkCollection()->setRepresentationType(Settings::TYPE_IDENTITY);
        $settings->getDefaultDataCollectionLinkCollection()->setHasReference(true);

        $expected = <<<EOT
        {
            "links" : {
                "posts.author" : {
                    "href": "http://example.com/people/{posts.author}",
                    "type": "people"
                },
                "posts.comments" : {
                    "href": "http://example.com/comments/{posts.comments}",
                    "type": "comments"
                }
            },
            "posts" : [
                {
                    "id"    : "1",
                    "title" : "Rails is Omakase",
                    "body"  : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla.",
                    "links" : {
                        "author"   : "9",
                        "comments" : [ "5", "12", "17", "20" ]
                    }
                },
                {
                    "id"    : "2",
                    "title" : "Lorem ipsum dolor",
                    "body"  : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla.",
                    "links" : {
                        "author"   : "9",
                        "comments" : [ "1", "2", "3" ]
                    }
                }
            ],
            "linked" : {
                "people" : [
                    {
                        "id"   : "9",
                        "name" : "John Author"
                    }
                ],
                "comments" : [
                    {
                        "id"   : "5",
                        "body" : "Parley is a discussion, especially one between enemies"
                    },
                    {
                        "id"   : "12",
                        "body" : "The parsley letter"
                    },
                    {
                        "id"   : "17",
                        "body" : "Dependency Injection is Not a Vice"
                    },
                    {
                        "id"   : "20",
                        "body" : "Outside every fat man there was an even fatter man trying to close in"
                    },
                    {
                        "id"   : "1",
                        "body" : "Mmmmmakase"
                    },
                    {
                        "id"   : "2",
                        "body" : "I prefer unagi"
                    },
                    {
                        "id"   : "3",
                        "body" : "What's Omakase?"
                    }
                ]
            }
        }
EOT;
        // remove formatting from 'expected'
        $expected = json_encode(json_decode($expected));

        $this->providerContainer = new ProviderContainer([
            Author::class  => AuthorProvider::class,
            Comment::class => CommentProvider::class,
            Post::class    => PostProviderNoUrlTemplates::class,
        ]);

        $encoder = new Encoder($settings, $this->providerContainer);
        $this->assertEquals($expected, $encoder->encode($this->posts));
    }
}
