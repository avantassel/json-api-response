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

class LinkToSingleGenerator extends BaseGenerator
{
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
            SettingsInterface::TYPE_IDENTITY.'])'
        );

        parent::__construct($representationType, $hasAttributes, $hasReference, $hasType);
    }

    /**
     * @param object   $resource
     * @param Provider $provider
     *
     * @return stdClass|string
     */
    public function generate($resource, Provider $provider)
    {
        assert('is_object($resource)');

        switch($this->getRepresentationType()) {
            case SettingsInterface::TYPE_IDENTITY:
                $result = $this->generateAsIdentity($resource, $provider);
                break;
            default:
                $result = $this->generateAsObject($resource, $provider);
                break;
        }

        return $result;
    }

    /**
     * @param object   $resource
     * @param Provider $provider
     *
     * @return string
     */
    protected function generateAsIdentity($resource, Provider $provider)
    {
        return $provider->getId($resource);
    }

    /**
     * @param object   $resource
     * @param Provider $provider
     *
     * @return stdClass
     */
    protected function generateAsObject($resource, Provider $provider)
    {
        return $this->generateBasicProperties($resource, $provider);
    }
}
