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
use \Neomerx\JsonApi\Contracts\SchemaProviderInterface as Provider;

abstract class BaseGenerator
{
    const PROPERTY_ID    = 'id';
    const PROPERTY_HREF  = 'href';
    const PROPERTY_TYPE  = 'type';

    /**
     * @var array
     */
    protected $reservedFieldNames = [
        self::PROPERTY_ID,
        self::PROPERTY_HREF,
        self::PROPERTY_TYPE,
    ];

    /**
     * @var int
     */
    private $representationType;

    /**
     * @var bool
     */
    private $hasAttributes;

    /**
     * @var bool
     */
    private $hasReference;

    /**
     * @var bool
     */
    private $hasType;

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
            'is_int($representationType) and is_bool($hasAttributes) and is_bool($hasReference) and is_bool($hasType)'
        );

        $this->representationType = $representationType;
        $this->hasAttributes      = $hasAttributes;
        $this->hasReference       = $hasReference;
        $this->hasType            = $hasType;
    }

    /**
     * @param object   $resource
     * @param Provider $provider
     *
     * @return stdClass
     */
    protected function generateBasicProperties($resource, Provider $provider)
    {
        assert('is_object($resource)');

        $representation = new stdClass();

        $resourceId = $provider->getId($resource);
        $representation->{self::PROPERTY_ID} = $resourceId;

        //  An ID MUST be a string which SHOULD only contain alphanumeric characters, dashes and underscores.
        // @see http://jsonapi.org/format/#document-structure-resource-object-ids
        assert('is_string($resourceId) and preg_match(\'/^[a-z_\-0-9]+$/i\', $resourceId)');

        if ($this->hasAttributes === true) {
            foreach ($provider->getAttributes($resource) as $fieldName => $value) {
                assert('is_string($fieldName) and in_array($fieldName, $this->reservedFieldNames) === false');
                assert('is_scalar($value)');
                $representation->{$fieldName} = $value;
            }
        }

        if ($this->hasReference === true) {
            $representation->{self::PROPERTY_HREF} = $provider->getHref([$resourceId]);
        }

        if ($this->hasType === true) {
            $representation->{self::PROPERTY_TYPE} = $provider->getJsonType();
        }

        return $representation;
    }

    /**
     * @return int
     */
    protected function getRepresentationType()
    {
        return $this->representationType;
    }

    /**
     * @return boolean
     */
    protected function hasAttributes()
    {
        return $this->hasAttributes;
    }

    /**
     * @return boolean
     */
    protected function hasReference()
    {
        return $this->hasReference;
    }

    /**
     * @return boolean
     */
    protected function hasType()
    {
        return $this->hasType;
    }
}
