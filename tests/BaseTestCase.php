<?php namespace Neomerx\JsonApi\Tests;

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

use \PHPUnit_Framework_TestCase;
use \Neomerx\JsonApi\Tests\Data\PostSchema;
use \Neomerx\JsonApi\Tests\Data\CommentSchema;

abstract class BaseTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $posts;

    /**
     * @var array
     */
    protected $comments;

    public function setUp()
    {
        parent::setUp();

        $this->posts = [
            PostSchema::create('1', 'Rails is Omakase'),
            PostSchema::create(
                '2',
                'Lorem ipsum dolor',
                null,
                [
                    CommentSchema::create('1', 'Mmmmmakase'),
                    CommentSchema::create('2', 'I prefer unagi'),
                    CommentSchema::create('3', 'What\'s Omakase?'),
                ]
            ),
        ];

        $this->comments = [
            CommentSchema::create('1', 'Mmmmmakase'),
            CommentSchema::create('2', 'I prefer unagi'),
        ];
    }
}
