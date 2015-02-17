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

abstract class AuthorSchema
{
    const PROPERTY_ID   = 'identity';
    const PROPERTY_NAME = 'name';

    /**
     * @param string $identity
     * @param string $name
     *
     * @return Author
     */
    public static function create($identity, $name)
    {
        $author = new Author();

        $author->{self::PROPERTY_ID}   = $identity;
        $author->{self::PROPERTY_NAME} = $name;

        return $author;
    }
}
