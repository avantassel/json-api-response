<?php namespace Neomerx\JsonApi\Generators;

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

use \stdClass;
use \Neomerx\JsonApi\Contracts\SettingsInterface;
use \Neomerx\JsonApi\Contracts\SchemaProviderInterface as Provider;

class LinkToCollectionGenerator extends BaseGenerator
{
    const PROPERTY_IDS = 'ids';

    /**
     * @param int  $representationType
     * @param bool $hasAttributes
     * @param bool $hasReference
     * @param bool $hasType
     */
    public function __construct(
        $representationType,
        $hasAttributes,
        $hasReference,
        $hasType
    ) {
        assert(
            'in_array($representationType, ['.
            SettingsInterface::TYPE_OBJECT.', '.
            SettingsInterface::TYPE_IDENTITY.', '.
            SettingsInterface::TYPE_COMBINED.'])'
        );

        parent::__construct($representationType, $hasAttributes, $hasReference, $hasType);
    }

    /**
     * @param array    $resources
     * @param Provider $provider
     *
     * @return array|object
     */
    public function generate(array $resources, Provider $provider)
    {
        switch($this->getRepresentationType()) {
            case SettingsInterface::TYPE_IDENTITY:
                $result = $this->generateAsIdentityCollection($resources, $provider);
                break;
            case SettingsInterface::TYPE_COMBINED:
                $result = $this->generateAsCombinedCollection($resources, $provider);
                break;
            default:
                $result = $this->generateAsObjectCollection($resources, $provider);
                break;
        }

        return $result;
    }

    /**
     * Represents collection as an array of identities.
     *
     * For example:
     * {
     *   "posts": ["1", "2"]
     * }
     *
     * @link http://jsonapi.org/format/#document-structure-resource-collection-representations
     *
     * @param array    $resources
     * @param Provider $provider
     *
     * @return array
     */
    protected function generateAsIdentityCollection(array $resources, Provider $provider)
    {
        $result = [];

        foreach ($resources as $resource) {
            $result[] = $provider->getId($resource);
        }

        return $result;
    }

    /**
     * Represents collection by a single "collection" object.
     *
     * For example:
     * {
     *   "comments": {
     *   "href": "http://example.com/comments/5,12,17,20",
     *   "ids": [ "5", "12", "17", "20" ],
     *   "type": "comments"
     *   }
     * }
     *
     * @link http://jsonapi.org/format/#document-structure-resource-collection-representations
     *
     * @param array    $resources
     * @param Provider $provider
     *
     * @return stdClass
     */
    protected function generateAsCombinedCollection(array $resources, Provider $provider)
    {
        $result = new stdClass();

        $identities = $this->generateAsIdentityCollection($resources, $provider);

        $result->{self::PROPERTY_HREF} = $provider->getHref($identities);
        $result->{self::PROPERTY_IDS}  = $identities;
        $result->{self::PROPERTY_TYPE} = $provider->getJsonType();

        return $result;
    }

    /**
     * Represents collection as an array of objects.
     *
     * For example:
     * {
     *   "posts": [{
     *     "id": "1"
     *       // ... attributes of this post
     *     }, {
     *       "id": "2"
     *     // ... attributes of this post
     *   }]
     * }
     *
     * @link http://jsonapi.org/format/#document-structure-resource-collection-representations
     *
     * @param array    $resources
     * @param Provider $provider
     *
     * @return array
     */
    protected function generateAsObjectCollection(array $resources, Provider $provider)
    {
        $result = [];

        foreach ($resources as $resource) {
            $result[] = $this->generateBasicProperties($resource, $provider);
        }

        return $result;
    }
}
