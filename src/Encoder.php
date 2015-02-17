<?php namespace Neomerx\JsonApi;

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
use \Neomerx\JsonApi\Generators\ResourceGenerator;
use \Neomerx\JsonApi\Generators\CollectionGenerator;
use \Neomerx\JsonApi\Generators\DocumentLinksGenerator;
use \Neomerx\JsonApi\Generators\DocumentLinkedGenerator;
use \Neomerx\JsonApi\Contracts\SettingsInterface as Settings;
use \Neomerx\JsonApi\Contracts\ContainerInterface as Container;
use \Neomerx\JsonApi\Contracts\SchemaProviderInterface as Provider;

class Encoder
{
    const KEY_PRIMARY_DEFAULT = 'data';
    const KEY_META            = 'meta';
    const KEY_LINKS           = 'links';
    const KEY_LINKED          = 'linked';

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param Settings $settings
     * @param Container $container
     */
    public function __construct(Settings $settings, Container $container)
    {
        $this->settings  = $settings;
        $this->container = $container;
    }

    /**
     * Get JSON API string representation for data.
     *
     * @param object|array|null $data
     * @param object|null       $meta
     * @param int               $options @see json_encode
     * @param int               $depth @see json_encode
     *
     * @return string
     */
    public function encode($data, $meta = null, $options = 0, $depth = 512)
    {
        return json_encode($this->compose($data, $meta), $options, $depth);
    }

    /**
     * Get JSON API object representation for data.
     *
     * @param object|array|null $data
     * @param object|null       $meta
     *
     * @return stdClass
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function compose($data, $meta = null)
    {
        assert('is_object($meta) || is_null($meta)');
        assert('is_object($data) || is_array($data) || is_null($data)');

        $result = new stdClass();

        $linkedGenerator = null;
        $mainResource    = null;
        $linksGenerator  = null;
        if ($data === null) {
            $jsonType       = self::KEY_PRIMARY_DEFAULT;
            $representation = null;
        } elseif (is_array($data) && empty($data) === true) {
            $jsonType       = self::KEY_PRIMARY_DEFAULT;
            $representation = [];
        } elseif (is_array($data) && empty($data) === false) {
            /** @var array $data */
            $mainResource    = $data[0];
            $linkedGenerator = $this->createDocumentLinkedGenerator($mainResource, $this->settings);
            $provider        = $this->container->getProvider($mainResource);
            $linksGenerator  = $this->createDocumentLinksGenerator($mainResource, $provider, $this->settings);
            $generator       = $this->createCollectionGenerator($data, $linkedGenerator, $linksGenerator);
            $representation  = $generator->generate($data, $provider);
            $jsonType        = $provider->getJsonType();
        } else {
            /** @var object $data */
            $mainResource    = $data;
            $linkedGenerator = $this->createDocumentLinkedGenerator($mainResource, $this->settings);
            $provider        = $this->container->getProvider($mainResource);
            $linksGenerator  = $this->createDocumentLinksGenerator($mainResource, $provider, $this->settings);
            $generator       = $this->createResourceGenerator($mainResource, $linkedGenerator, $linksGenerator);
            $representation  = $generator->generate($mainResource, $provider);
            $jsonType        = $provider->getJsonType();
        }

        if (isset($meta) && isset($mainResource) && $this->settings->meta($mainResource)->isVisible()) {
            $result->{self::KEY_META} = $meta;
        }

        if (isset($linksGenerator) === true && $this->settings->links($mainResource)->isVisible()) {
            $result->{self::KEY_LINKS} = $linksGenerator->generate();
        }

        $result->{$jsonType} = $representation;

        if ($this->settings->linked($mainResource)->isVisible() && isset($linkedGenerator) === true) {
            $result->{self::KEY_LINKED} = $linkedGenerator->generate();
        }

        return $result;
    }

    /**
     * @param array                   $resources
     * @param DocumentLinkedGenerator $linkedGenerator
     * @param DocumentLinksGenerator  $linksGenerator
     *
     * @return CollectionGenerator
     */
    protected function createCollectionGenerator(
        array $resources,
        DocumentLinkedGenerator $linkedGenerator,
        DocumentLinksGenerator $linksGenerator
    ) {
        assert('empty($resources) === false');

        $firstResource = $resources[0];

        $settings = $this->settings->dataCollection($firstResource);

        return new CollectionGenerator(
            $settings->getRepresentationType(),
            $settings->hasAttributes(),
            $settings->hasLinks(),
            $settings->hasReference(),
            $settings->hasType(),
            $this->settings->links($firstResource)->isVisible(),
            $this->settings,
            $this->container,
            $linkedGenerator,
            $linksGenerator
        );
    }

    /**
     * @param object                  $resource
     * @param DocumentLinkedGenerator $linkedGenerator
     * @param DocumentLinksGenerator  $linksGenerator
     *
     * @return ResourceGenerator
     */
    protected function createResourceGenerator(
        $resource,
        DocumentLinkedGenerator $linkedGenerator,
        DocumentLinksGenerator $linksGenerator
    ) {
        assert('is_object($resource) === true');

        $settings = $this->settings->dataSingle($resource);

        return new ResourceGenerator(
            $settings->getRepresentationType(),
            $settings->hasAttributes(),
            $settings->hasLinks(),
            $settings->hasReference(),
            $settings->hasType(),
            $this->settings->links($resource)->isVisible(),
            $this->settings,
            $this->container,
            $linkedGenerator,
            $linksGenerator
        );
    }

    /**
     * @param object   $resource
     * @param Settings $settings
     *
     * @return DocumentLinkedGenerator
     */
    protected function createDocumentLinkedGenerator($resource, Settings $settings)
    {
        return new DocumentLinkedGenerator($resource, $settings);
    }

    /**
     * @param object   $resource
     * @param Provider $provider
     * @param Settings $settings
     *
     * @return DocumentLinksGenerator
     */
    protected function createDocumentLinksGenerator($resource, Provider $provider, Settings $settings)
    {
        return new DocumentLinksGenerator($resource, $provider, $settings);
    }
}
