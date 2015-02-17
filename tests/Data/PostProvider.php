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

class PostProvider extends ProviderBase
{
    public function __construct()
    {
        parent::__construct(PostSchema::PROPERTY_ID, '/posts/', 'posts', 'http://example.com/');
        $this->setUrlTemplates([
            'comments' => '/comments?posts={posts.id}',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($post)
    {
        assert('$post instanceof '.Post::class);

        return [
            PostSchema::PROPERTY_TITLE => $post->{PostSchema::PROPERTY_TITLE},
            PostSchema::PROPERTY_BODY  => $post->{PostSchema::PROPERTY_BODY},
        ];
    }

    /**
     * @inheritdoc
     */
    public function getLinks($post)
    {
        assert('$post instanceof '.Post::class);

        return [
            'author'   => $post->{PostSchema::PROPERTY_AUTHOR},
            'comments' => $post->{PostSchema::PROPERTY_COMMENTS},
        ];
    }
}
