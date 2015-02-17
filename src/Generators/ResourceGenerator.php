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
use \Neomerx\JsonApi\Generators\Traits\LinksTrait;
use \Neomerx\JsonApi\Contracts\SettingsInterface as Settings;
use \Neomerx\JsonApi\Contracts\ContainerInterface as Container;
use \Neomerx\JsonApi\Contracts\SchemaProviderInterface as Provider;

class ResourceGenerator extends LinkToSingleGenerator
{
    use LinksTrait;

    const PROPERTY_LINKS = 'links';

    /**
     * @var bool
     */
    private $hasLinks;

    /**
     * @var bool
     */
    private $documentHasLinks;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var DocumentLinkedGenerator
     */
    private $linkedGenerator;

    /**
     * @var DocumentLinksGenerator
     */
    private $linksGenerator;

    /**
     * @param int                     $representationType
     * @param bool                    $hasAttributes
     * @param bool                    $hasLinks
     * @param bool                    $hasReference
     * @param bool                    $hasType
     * @param bool                    $documentHasLinks
     * @param Settings                $settings
     * @param Container               $container
     * @param DocumentLinkedGenerator $linkedGenerator
     * @param DocumentLinksGenerator  $linksGenerator
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $representationType,
        $hasAttributes,
        $hasLinks,
        $hasReference,
        $hasType,
        $documentHasLinks,
        Settings $settings,
        Container $container,
        DocumentLinkedGenerator $linkedGenerator,
        DocumentLinksGenerator $linksGenerator
    ) {
        assert('is_bool($hasLinks) && is_bool($documentHasLinks)');

        parent::__construct($representationType, $hasAttributes, $hasReference, $hasType);

        $this->hasLinks             = $hasLinks;
        $this->settings             = $settings;
        $this->container            = $container;
        $this->linksGenerator       = $linksGenerator;
        $this->linkedGenerator      = $linkedGenerator;
        $this->documentHasLinks     = $documentHasLinks;
        $this->reservedFieldNames[] = self::PROPERTY_LINKS;
    }

    /**
     * @param object   $resource
     * @param Provider $provider
     *
     * @return stdClass
     */
    protected function generateAsObject($resource, Provider $provider)
    {
        $result = $this->generateBasicProperties($resource, $provider);

        $links = new stdClass();
        if ($this->documentHasLinks === true || $this->hasLinks === true) {
            $this->generateLinks($resource, $provider, $links);
        }

        if ($this->hasLinks === true) {
            $result->{self::PROPERTY_LINKS} = $links;
        }

        if ($this->documentHasLinks === true) {
            // if any URL templates are specified for this type add them to document's link section
            $this->addUrlTemplates($provider, $this->linksGenerator);
        }

        return $result;
    }

    /**
     * @param object   $resource
     * @param Provider $provider
     * @param stdClass $links
     *
     * @return void
     */
    private function generateLinks($resource, Provider $provider, stdClass $links)
    {
        foreach ($provider->getLinks($resource) as $linksProperty => $linkedResource) {
            assert('is_string($linksProperty)');
            assert('is_object($linkedResource) || is_array($linkedResource) || is_null($linkedResource)');

            if (is_array($linkedResource) === true) {
                if (empty($linkedResource) === true) {
                    if ($this->hasLinks === true) {
                        $links->{$linksProperty} = [];
                    }
                    continue;
                }

                $this->addLinksToCollection($resource, $linksProperty, $linkedResource, $links);

            } else {
                if (empty($linkedResource) === true) {
                    if ($this->hasLinks === true) {
                        $links->{$linksProperty} = null;
                    }
                    continue;
                }

                $this->addLinkToSingle($resource, $linksProperty, $linkedResource, $links);

            }
        }
    }

    /**
     * @param object   $resource
     * @param string   $linksProperty
     * @param array    $linkedResources
     * @param stdClass $links
     *
     * @return void
     */
    private function addLinksToCollection($resource, $linksProperty, array $linkedResources, stdClass $links)
    {
        $firstLinkedResource = $linkedResources[0];
        $linkedProvider      = $this->container->getProvider($firstLinkedResource);
        $settings            = $this->settings->dataSingleLinkCollection($resource, $firstLinkedResource);

        // if links should be shown && they should be on document level...
        if ($settings->hasReference() === true && $this->documentHasLinks === true) {
            // ... turn off show href in links and ...
            $settings->setHasReference(false);
            // ... add it to document's level
            $this->linksGenerator->addByProperty($linksProperty, $linkedProvider);
        }

        $links->{$linksProperty} = $this->generateLinkToCollection(
            $linkedResources,
            $linkedProvider,
            $this->linkedGenerator,
            $settings
        );
    }

    /**
     * @param object   $resource
     * @param string   $linksProperty
     * @param object   $linkedResource
     * @param stdClass $links
     *
     * @return void
     */
    private function addLinkToSingle($resource, $linksProperty, $linkedResource, stdClass $links)
    {
        $linkedProvider = $this->container->getProvider($linkedResource);
        $settings       = $this->settings->dataSingleLinkSingle($resource, $linkedResource);

        // if links should be shown and they should be on document level...
        if ($settings->hasReference() === true && $this->documentHasLinks === true) {
            // ... turn off show href in links and ...
            $settings->setHasReference(false);
            // ... add it to document's level
            $this->linksGenerator->addByProperty($linksProperty, $linkedProvider);
        }

        $links->{$linksProperty} = $this
            ->generateLinkToSingle($linkedResource, $linkedProvider, $this->linkedGenerator, $settings);
    }
}
