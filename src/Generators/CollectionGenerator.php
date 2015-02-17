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

class CollectionGenerator extends LinkToCollectionGenerator
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
        assert('is_bool($hasLinks) and is_bool($documentHasLinks)');

        parent::__construct($representationType, $hasAttributes, $hasReference, $hasType);

        $this->hasLinks             = $hasLinks;
        $this->settings             = $settings;
        $this->container            = $container;
        $this->documentHasLinks     = $documentHasLinks;
        $this->reservedFieldNames[] = self::PROPERTY_LINKS;
        $this->linksGenerator       = $linksGenerator;
        $this->linkedGenerator      = $linkedGenerator;
    }

    /**
     * @param array    $resources
     * @param Provider $provider
     *
     * @return array
     */
    protected function generateAsObjectCollection(array $resources, Provider $provider)
    {
        $result = [];

        $forceHideLinkHref = false;

        // if document has links section then place there urls resource links
        if ($this->documentHasLinks === true and empty($resources) === false) {
            $resource = $resources[0];

            foreach ($provider->getLinks($resources[0]) as $linksProperty => $linkedResource) {
                if (empty($linkedResource) === true) {
                    continue;
                }

                if (is_array($linkedResource) === true) {
                    $linkedResource = $linkedResource[0];
                    $hasReference   = $this->settings
                        ->dataCollectionLinkCollection($resource, $linkedResource)->hasReference();
                } else {
                    $hasReference = $this->settings
                        ->dataCollectionLinkSingle($resource, $linkedResource)->hasReference();
                }

                $linkedProvider = $this->container->getProvider($linkedResource);
                // if links for this type should be shown...
                if ($hasReference === true) {
                    /// ... add it to document's level
                    $this->linksGenerator->addByProperty($linksProperty, $linkedProvider);
                }
            }

            $forceHideLinkHref = true;
        }

        foreach ($resources as $resource) {
            $result[] = $this->generateObject($resource, $provider, $forceHideLinkHref);
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
     * @param bool     $forceHideLinkHref
     *
     * @return stdClass
     */
    private function generateObject($resource, Provider $provider, $forceHideLinkHref)
    {
        $result = $this->generateBasicProperties($resource, $provider);

        if ($this->hasLinks === false) {
            return $result;
        }

        $links = new stdClass();

        foreach ($provider->getLinks($resource) as $linksProperty => $linkedResource) {
            if (is_array($linkedResource) === true) {
                if (empty($linkedResource) === true) {
                    $links->{$linksProperty} = [];
                    continue;
                }

                $firstLinkedResource = $linkedResource[0];
                $linkedProvider      = $this->container->getProvider($firstLinkedResource);
                $settings            = $this->settings->dataCollectionLinkCollection($resource, $firstLinkedResource);

                if ($forceHideLinkHref === true) {
                    $settings->setHasReference(false);
                }

                $links->{$linksProperty} = $this->generateLinkToCollection(
                    $linkedResource,
                    $linkedProvider,
                    $this->linkedGenerator,
                    $settings
                );

            } else {
                if (empty($linkedResource) === true) {
                    $links->{$linksProperty} = null;
                    continue;
                }

                $linkedProvider = $this->container->getProvider($linkedResource);
                $settings       = $this->settings->dataCollectionLinkSingle($resource, $linkedResource);

                if ($forceHideLinkHref === true) {
                    $settings->setHasReference(false);
                }

                $links->{$linksProperty} = $this->generateLinkToSingle(
                    $linkedResource,
                    $linkedProvider,
                    $this->linkedGenerator,
                    $settings
                );
            }

        }
        $result->{self::PROPERTY_LINKS} = $links;

        return $result;
    }
}
