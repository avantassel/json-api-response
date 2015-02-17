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
use \Neomerx\JsonApi\Settings\LinkedResourceRepresentation;
use \Neomerx\JsonApi\Contracts\SettingsInterface as Settings;
use \Neomerx\JsonApi\Contracts\SchemaProviderInterface as Provider;

class DocumentLinkedGenerator
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var object
     */
    private $mainResource;

    /**
     * [ jsonType => [... actual resources of that type ...], ...]
     *
     * @var array
     */
    private $addedResources = [];

    /**
     * [ jsonType => settings, ... ]
     *
     * @var array
     */
    private $generationSettings = [];

    /**
     * [ jsonType => provider, ... ]
     *
     * @var array
     */
    private $providers = [];

    /**
     * @var bool
     */
    private $isVisible;

    /**
     * @param object   $mainResource
     * @param Settings $settings
     */
    public function __construct($mainResource, Settings $settings)
    {
        assert('is_object($mainResource)');

        $this->isVisible = $settings->linked($mainResource)->isVisible();
        if ($this->isVisible === false) {
            return;
        }

        $this->settings           = $settings;
        $this->mainResource       = $mainResource;
        $this->providers          = [];
        $this->addedResources     = [];
        $this->generationSettings = [];
    }

    /**
     * @param object   $resource
     * @param Provider $provider
     *
     * @return void
     */
    public function add($resource, Provider $provider)
    {
        assert('is_object($resource)');

        if ($this->isVisible === false) {
            return;
        }

        $jsonType = $provider->getJsonType();

        // add resource
        if (isset($this->addedResources[$jsonType]) === false) {
            $this->addedResources[$jsonType] = [];
        }
        $resourceId = $provider->getId($resource);
        $this->addResource($jsonType, $resourceId, $resource);

        // add generation settings
        if (isset($this->generationSettings[$jsonType]) === false) {
            $this->generationSettings[$jsonType] = $this->settings->linkedResource($this->mainResource, $resource);
        }

        $this->addProvider($jsonType, $provider);
    }

    /**
     * @param array    $resources
     * @param Provider $provider
     *
     * @return void
     */
    public function addArray(array $resources, Provider $provider)
    {
        if ($this->isVisible === false) {
            return;
        }

        $jsonType = $provider->getJsonType();

        // add resources
        if (isset($this->addedResources[$jsonType]) === false) {
            $this->addedResources[$jsonType] = [];
        }
        foreach ($resources as $resource) {
            $this->addResource($jsonType, $provider->getId($resource), $resource);
        }

        // add generation settings
        if (isset($this->generationSettings[$jsonType]) === false && empty($resources) === false) {
            $this->generationSettings[$jsonType] = $this->settings->linkedResource($this->mainResource, $resources[0]);
        }

        $this->addProvider($jsonType, $provider);
    }

    /**
     * @return stdClass|null
     */
    public function generate()
    {
        if ($this->isVisible === false) {
            return null;
        }

        $result = new stdClass();

        foreach ($this->addedResources as $jsonType => $linkedArray) {
            $settings = $this->getGenerationSettings($jsonType);
            $generator = new LinkToCollectionGenerator(
                Settings::TYPE_OBJECT,
                $settings->hasAttributes(),
                $settings->hasReference(),
                $settings->hasType()
            );
            $result->{$jsonType} = $generator->generate($linkedArray, $this->getProvider($jsonType));
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAdded()
    {
        return $this->addedResources;
    }

    /**
     * @param string $jsonType
     *
     * @return LinkedResourceRepresentation
     */
    private function getGenerationSettings($jsonType)
    {
        return $this->generationSettings[$jsonType];
    }

    /**
     * @param string $jsonType
     *
     * @return Provider
     */
    private function getProvider($jsonType)
    {
        return $this->providers[$jsonType];
    }

    /**
     * @param string   $jsonType
     * @param Provider $provider
     *
     * @return void
     */
    private function addProvider($jsonType, Provider $provider)
    {
        if (isset($this->providers[$jsonType]) === false) {
            $this->providers[$jsonType] = $provider;
        }
    }

    /**
     * @param string $jsonType
     * @param string $resourceId
     * @param object $resource
     *
     * @return void
     */
    private function addResource($jsonType, $resourceId, $resource)
    {
        assert('is_string($jsonType) && is_string($resourceId) && is_object($resource)');

        if (isset($this->addedResources[$jsonType][$resourceId]) === false) {
            $this->addedResources[$jsonType][$resourceId] = $resource;
        }
    }
}
