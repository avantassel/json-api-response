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
use \Neomerx\JsonApi\Settings\LinksRepresentation;
use \Neomerx\JsonApi\Contracts\SettingsInterface as Settings;
use \Neomerx\JsonApi\Contracts\SchemaProviderInterface as Provider;

class DocumentLinksGenerator
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * [ dot separated jsonTypes => url, ...]
     *
     * @var array
     */
    private $addedLinks = [];

    /**
     * @var LinksRepresentation
     */
    private $generationSettings;

    /**
     * @var Provider
     */
    private $mainProvider;

    /**
     * @param object   $mainResource
     * @param Provider $mainProvider
     * @param Settings $settings
     */
    public function __construct($mainResource, Provider $mainProvider, Settings $settings)
    {
        assert('is_object($mainResource)');

        $this->generationSettings = $settings->links($mainResource);
        if ($this->generationSettings->isVisible() === false) {
            return;
        }

        $this->addedLinks   = [];
        $this->settings     = $settings;
        $this->mainProvider = $mainProvider;
    }

    /**
     * @param string   $property
     * @param Provider $provider
     *
     */
    public function addByProperty($property, Provider $provider)
    {
        assert('is_string($property)');

        if ($this->generationSettings->isVisible() === false) {
            return;
        }

        $url          = $provider->getHref([]);
        $mainJsonType = $this->mainProvider->getJsonType();

        $this->addLink([$mainJsonType, $property], $url.'{'.$mainJsonType.'.'.$property.'}', $provider->getJsonType());
    }

    /**
     * @param array  $paths
     * @param string $url
     * @param string $linkJsonType
     *
     * @return void
     */
    public function addLink(array $paths, $url, $linkJsonType)
    {
        assert('is_string($url) === true and is_string($linkJsonType) === true');
        assert('empty($paths) === false and empty($url) === false and empty($linkJsonType) === false');

        if ($this->generationSettings->isVisible() === false) {
            return;
        }

        $this->addedLinks[implode('.', $paths)] = [$url, $linkJsonType];
    }

    /**
     * @return object|null
     */
    public function generate()
    {
        if ($this->generationSettings->isVisible() === false) {
            return null;
        }

        $result = new stdClass();

        foreach ($this->addedLinks as $path => list($url, $jsonType)) {
            if ($this->generationSettings->getRepresentationType() === Settings::TYPE_IDENTITY) {
                $value = $url;
            } else {
                $value = new stdClass();
                $value->{BaseGenerator::PROPERTY_HREF} = $url;
                if ($this->generationSettings->hasType() === true) {
                    $value->{BaseGenerator::PROPERTY_TYPE} = $jsonType;
                }
            }

            $result->{$path} = $value;
        }

        return $result;
    }
}
