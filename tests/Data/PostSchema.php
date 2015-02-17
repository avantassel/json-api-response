<?php namespace Neomerx\JsonApi\Tests\Data;

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

abstract class PostSchema
{
    const PROPERTY_ID       = 'identity';
    const PROPERTY_TITLE    = 'title';
    const PROPERTY_BODY     = 'body';
    const PROPERTY_AUTHOR   = 'author';
    const PROPERTY_COMMENTS = 'comments';

    /**
     * @param string $identity
     * @param string $title
     * @param Author $author
     * @param array  $comments
     * @param string $body
     *
     * @return Post
     */
    public static function create(
        $identity,
        $title,
        $author = null,
        array $comments = null,
        $body = null
    ) {
        $body = $body !== null ? $body : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla.';
        if ($author === null) {
            $author = AuthorSchema::create('9', 'John Author');
        }
        if (empty($comments)) {
            $comments = [
                CommentSchema::create('5', 'Parley is a discussion, especially one between enemies'),
                CommentSchema::create('12', 'The parsley letter'),
                CommentSchema::create('17', 'Dependency Injection is Not a Vice'),
                CommentSchema::create('20', 'Outside every fat man there was an even fatter man trying to close in'),
            ];
        }

        $post = new Post();
        $post->{self::PROPERTY_ID}       = $identity;
        $post->{self::PROPERTY_TITLE}    = $title;
        $post->{self::PROPERTY_BODY}     = $body;
        $post->{self::PROPERTY_AUTHOR}   = $author;
        $post->{self::PROPERTY_COMMENTS} = $comments;

        return $post;
    }
}
