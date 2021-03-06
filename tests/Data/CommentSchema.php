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

abstract class CommentSchema
{
    const PROPERTY_ID    = 'identity';
    const PROPERTY_BODY  = 'body';

    /**
     * @param string $identity
     * @param string $body
     *
     * @return Comment
     */
    public static function create($identity, $body = null)
    {
        $body = $body !== null ? $body : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fringilla.';

        $post = new Comment();
        $post->{self::PROPERTY_ID}   = $identity;
        $post->{self::PROPERTY_BODY} = $body;

        return $post;
    }
}
