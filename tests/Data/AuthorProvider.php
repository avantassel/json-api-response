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

use \Neomerx\JsonApi\Schema\ProviderBase;
use \Prophecy\Exception\Doubler\MethodNotFoundException;

class AuthorProvider extends ProviderBase
{
    public function __construct()
    {
        parent::__construct(AuthorSchema::PROPERTY_ID, 'http://example.com/people/', 'people');
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($author)
    {
        return [
            AuthorSchema::PROPERTY_NAME => $author->{AuthorSchema::PROPERTY_NAME},
        ];
    }

    /**
     * @inheritdoc
     */
    public function getLinks($resource)
    {
        throw new MethodNotFoundException('Method is not implemented', __CLASS__, __METHOD__);
    }
}
