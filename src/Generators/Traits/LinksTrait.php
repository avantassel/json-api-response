<?php namespace Neomerx\JsonApi\Generators\Traits;

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
use \Neomerx\JsonApi\Generators\LinkToSingleGenerator;
use \Neomerx\JsonApi\Generators\DocumentLinksGenerator;
use \Neomerx\JsonApi\Generators\DocumentLinkedGenerator;
use \Neomerx\JsonApi\Generators\LinkToCollectionGenerator;
use \Neomerx\JsonApi\Settings\DataLinkSingleRepresentation;
use \Neomerx\JsonApi\Settings\DataLinkCollectionRepresentation;
use \Neomerx\JsonApi\Contracts\SchemaProviderInterface as Provider;

trait LinksTrait
{

    /**
     * @param array                            $linkedResources
     * @param Provider                         $linkedProvider
     * @param DocumentLinkedGenerator          $linkedGenerator
     * @param DataLinkCollectionRepresentation $settings
     *
     * @return array|stdClass
     */
    protected function generateLinkToCollection(
        array $linkedResources,
        Provider $linkedProvider,
        DocumentLinkedGenerator $linkedGenerator,
        DataLinkCollectionRepresentation $settings
    ) {
        assert('empty($linkedResources) === false');

        $generator = new LinkToCollectionGenerator(
            $settings->getRepresentationType(),
            $settings->hasAttributes(),
            $settings->hasReference(),
            $settings->hasType()
        );

        $linkedGenerator->addArray($linkedResources, $linkedProvider);

        return $generator->generate($linkedResources, $linkedProvider);
    }

    /**
     * @param object                       $linkedResource
     * @param Provider                     $linkedProvider
     * @param DocumentLinkedGenerator      $linkedGenerator
     * @param DataLinkSingleRepresentation $settings
     *
     * @return string|stdClass
     */
    protected function generateLinkToSingle(
        $linkedResource,
        Provider $linkedProvider,
        DocumentLinkedGenerator $linkedGenerator,
        DataLinkSingleRepresentation $settings
    ) {
        assert('$linkedResource !== null');

        $generator = new LinkToSingleGenerator(
            $settings->getRepresentationType(),
            $settings->hasAttributes(),
            $settings->hasReference(),
            $settings->hasType()
        );

        $linkedGenerator->add($linkedResource, $linkedProvider);

        return $generator->generate($linkedResource, $linkedProvider);
    }


    /**
     * @param Provider               $provider
     * @param DocumentLinksGenerator $generator
     *
     * @return void
     */
    protected function addUrlTemplates(Provider $provider, DocumentLinksGenerator $generator)
    {
        foreach ($provider->getUrls() as $linkedProperty => list($url, $linkedJsonType)) {
            $generator->addLink([$provider->getJsonType(), $linkedProperty], $url, $linkedJsonType);
        }
    }
}
