<?php namespace Neomerx\JsonApi\Settings;

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

use \Neomerx\JsonApi\Contracts\SettingsInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Settings implements SettingsInterface
{
    const SET_VISIBLE        = 'setVisible';
    const SET_HAS_REFERENCE  = 'setHasType';
    const SET_HAS_TYPE       = 'setHasType';
    const SET_HAS_LINKS      = 'setHasLinks';
    const SET_HAS_ATTRIBUTES = 'setHasAttributes';
    const SET_REPRESENTATION = 'setRepresentationType';

    /**
     * @var MetaRepresentation
     */
    private $meta;

    /**
     * @var LinksRepresentation
     */
    private $links;

    /**
     * @var LinkedRepresentation
     */
    private $linked;

    /**
     * @var LinkedResourceRepresentation
     */
    private $linkedResource;

    /**
     * @var DataSingleRepresentation
     */
    private $dataSingle;

    /**
     * @var DataCollectionRepresentation
     */
    private $dataCollection;

    /**
     * @var DataLinkSingleRepresentation
     */
    private $dataSingleLinkSingle;

    /**
     * @var DataLinkSingleRepresentation
     */
    private $dataCollectionLinkSingle;

    /**
     * @var DataLinkCollectionRepresentation
     */
    private $dataSingleLinkCollection;

    /**
     * @var DataLinkCollectionRepresentation
     */
    private $dataCollectionLinkCollection;

    /**
     * @var array
     */
    private $alterMeta;

    /**
     * @var array
     */
    private $alterLinks;

    /**
     * @var array
     */
    private $alterLinked;

    /**
     * @var array
     */
    private $alterLinkedResource;

    /**
     * @var array
     */
    private $alterDataSingle;

    /**
     * @var array
     */
    private $alterDataCollection;

    /**
     * @var array
     */
    private $alterDataSingleLinkSingle;

    /**
     * @var array
     */
    private $alterDataCollectionLinkSingle;

    /**
     * @var array
     */
    private $alterDataSingleLinkCollection;

    /**
     * @var array
     */
    private $alterDataCollectionLinkCollection;

    /**
     * @param MetaRepresentation               $meta                              Default settings
     * @param LinksRepresentation              $links                             Default settings
     * @param LinkedRepresentation             $linked                            Default settings
     * @param LinkedResourceRepresentation     $linkedResource                    Default settings
     * @param DataSingleRepresentation         $dataSingle                        Default settings
     * @param DataCollectionRepresentation     $dataCollection                    Default settings
     * @param DataLinkSingleRepresentation     $dataSingleLinkSingle              Default settings
     * @param DataLinkSingleRepresentation     $dataCollectionLinkSingle          Default settings
     * @param DataLinkCollectionRepresentation $dataSingleLinkCollection          Default settings
     * @param DataLinkCollectionRepresentation $dataCollectionLinkCollection      Default settings
     * @param array                            $alterMeta                         Alteration to defaults
     * @param array                            $alterLinks                        Alteration to defaults
     * @param array                            $alterLinked                       Alteration to defaults
     * @param array                            $alterLinkedResource               Alteration to defaults
     * @param array                            $alterDataSingle                   Alteration to defaults
     * @param array                            $alterDataCollection               Alteration to defaults
     * @param array                            $alterDataSingleLinkSingle         Alteration to defaults
     * @param array                            $alterDataCollectionLinkSingle     Alteration to defaults
     * @param array                            $alterDataSingleLinkCollection     Alteration to defaults
     * @param array                            $alterDataCollectionLinkCollection Alteration to defaults
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        MetaRepresentation $meta,
        LinksRepresentation $links,
        LinkedRepresentation $linked,
        LinkedResourceRepresentation $linkedResource,
        DataSingleRepresentation $dataSingle,
        DataCollectionRepresentation $dataCollection,
        DataLinkSingleRepresentation $dataSingleLinkSingle,
        DataLinkSingleRepresentation $dataCollectionLinkSingle,
        DataLinkCollectionRepresentation $dataSingleLinkCollection,
        DataLinkCollectionRepresentation $dataCollectionLinkCollection,
        array $alterMeta = [],
        array $alterLinks = [],
        array $alterLinked = [],
        array $alterLinkedResource = [],
        array $alterDataSingle = [],
        array $alterDataCollection = [],
        array $alterDataSingleLinkSingle = [],
        array $alterDataCollectionLinkSingle = [],
        array $alterDataSingleLinkCollection = [],
        array $alterDataCollectionLinkCollection = []
    ) {
        $this->meta                              = $meta;
        $this->links                             = $links;
        $this->linked                            = $linked;
        $this->linkedResource                    = $linkedResource;
        $this->dataSingle                        = $dataSingle;
        $this->dataCollection                    = $dataCollection;
        $this->dataSingleLinkSingle              = $dataSingleLinkSingle;
        $this->dataCollectionLinkSingle          = $dataCollectionLinkSingle;
        $this->dataSingleLinkCollection          = $dataSingleLinkCollection;
        $this->dataCollectionLinkCollection      = $dataCollectionLinkCollection;
        $this->alterMeta                         = $alterMeta;
        $this->alterLinks                        = $alterLinks;
        $this->alterLinked                       = $alterLinked;
        $this->alterLinkedResource               = $alterLinkedResource;
        $this->alterDataSingle                   = $alterDataSingle;
        $this->alterDataCollection               = $alterDataCollection;
        $this->alterDataSingleLinkSingle         = $alterDataSingleLinkSingle;
        $this->alterDataCollectionLinkSingle     = $alterDataCollectionLinkSingle;
        $this->alterDataSingleLinkCollection     = $alterDataSingleLinkCollection;
        $this->alterDataCollectionLinkCollection = $alterDataCollectionLinkCollection;
    }

    /**
     * @param object $resource
     *
     * @return MetaRepresentation
     */
    public function meta($resource)
    {
        return $this->merge1($resource, $this->meta, $this->alterMeta, [self::SET_VISIBLE]);
    }

    /**
     * @param object $resource
     *
     * @return LinksRepresentation
     */
    public function links($resource)
    {
        return $this->merge1($resource, $this->links, $this->alterLinks, [self::SET_VISIBLE]);
    }

    /**
     * @param object $resource
     *
     * @return LinkedRepresentation
     */
    public function linked($resource)
    {
        return $this->merge1($resource, $this->linked, $this->alterLinked, [self::SET_VISIBLE]);
    }

    /**
     * @param object $resource
     * @param object $linkedResource
     *
     * @return LinkedResourceRepresentation
     */
    public function linkedResource($resource, $linkedResource)
    {
        return $this->merge2($resource, $linkedResource, $this->linkedResource, $this->alterLinkedResource, [
            self::SET_HAS_TYPE,
            self::SET_HAS_REFERENCE,
            self::SET_HAS_ATTRIBUTES,
        ]);
    }

    /**
     * @param object $resource
     *
     * @return DataSingleRepresentation
     */
    public function dataSingle($resource)
    {
        return $this->merge1($resource, $this->dataSingle, $this->alterDataSingle, [
            self::SET_HAS_TYPE,
            self::SET_HAS_LINKS,
            self::SET_HAS_REFERENCE,
            self::SET_HAS_ATTRIBUTES,
            self::SET_REPRESENTATION,
        ]);
    }

    /**
     * @param object $resource
     *
     * @return DataCollectionRepresentation
     */
    public function dataCollection($resource)
    {
        return $this->merge1($resource, $this->dataCollection, $this->alterDataCollection, [
            self::SET_HAS_TYPE,
            self::SET_HAS_LINKS,
            self::SET_HAS_REFERENCE,
            self::SET_HAS_ATTRIBUTES,
            self::SET_REPRESENTATION,
        ]);
    }

    /**
     * @param object $resource
     * @param object $linkResource
     *
     * @return DataLinkSingleRepresentation
     */
    public function dataSingleLinkSingle($resource, $linkResource)
    {
        return $this->merge2(
            $resource,
            $linkResource,
            $this->dataSingleLinkSingle,
            $this->alterDataSingleLinkSingle,
            [
                self::SET_HAS_TYPE,
                self::SET_HAS_REFERENCE,
                self::SET_HAS_ATTRIBUTES,
                self::SET_REPRESENTATION,
            ]
        );
    }

    /**
     * @param object $resource
     * @param object $linkResource
     *
     * @return DataLinkSingleRepresentation
     */
    public function dataCollectionLinkSingle($resource, $linkResource)
    {
        return $this->merge2(
            $resource,
            $linkResource,
            $this->dataCollectionLinkSingle,
            $this->alterDataCollectionLinkSingle,
            [
                self::SET_HAS_TYPE,
                self::SET_HAS_REFERENCE,
                self::SET_HAS_ATTRIBUTES,
                self::SET_REPRESENTATION,
            ]
        );
    }

    /**
     * @param object $resource
     * @param object $linkResource
     *
     * @return DataLinkCollectionRepresentation
     */
    public function dataSingleLinkCollection($resource, $linkResource)
    {
        return $this->merge2(
            $resource,
            $linkResource,
            $this->dataSingleLinkCollection,
            $this->alterDataSingleLinkCollection,
            [
                self::SET_HAS_TYPE,
                self::SET_HAS_REFERENCE,
                self::SET_HAS_ATTRIBUTES,
                self::SET_REPRESENTATION,
            ]
        );
    }

    /**
     * @param object $resource
     * @param object $linkResource
     *
     * @return DataLinkCollectionRepresentation
     */
    public function dataCollectionLinkCollection($resource, $linkResource)
    {
        return $this->merge2(
            $resource,
            $linkResource,
            $this->dataCollectionLinkCollection,
            $this->alterDataCollectionLinkCollection,
            [
                self::SET_HAS_TYPE,
                self::SET_HAS_REFERENCE,
                self::SET_HAS_ATTRIBUTES,
                self::SET_REPRESENTATION,
            ]
        );
    }

    /**
     * @return MetaRepresentation
     */
    public function getDefaultMeta()
    {
        return $this->meta;
    }

    /**
     * @return LinksRepresentation
     */
    public function getDefaultLinks()
    {
        return $this->links;
    }

    /**
     * @return LinkedRepresentation
     */
    public function getDefaultLinked()
    {
        return $this->linked;
    }

    /**
     * @return LinkedResourceRepresentation
     */
    public function getDefaultLinkedResource()
    {
        return $this->linkedResource;
    }

    /**
     * @return DataSingleRepresentation
     */
    public function getDefaultDataSingle()
    {
        return $this->dataSingle;
    }

    /**
     * @return DataCollectionRepresentation
     */
    public function getDefaultDataCollection()
    {
        return $this->dataCollection;
    }

    /**
     * @return DataLinkSingleRepresentation
     */
    public function getDefaultDataSingleLinkSingle()
    {
        return $this->dataSingleLinkSingle;
    }

    /**
     * @return DataLinkSingleRepresentation
     */
    public function getDefaultDataCollectionLinkSingle()
    {
        return $this->dataCollectionLinkSingle;
    }

    /**
     * @return DataLinkCollectionRepresentation
     */
    public function getDefaultDataSingleLinkCollection()
    {
        return $this->dataSingleLinkCollection;
    }

    /**
     * @return DataLinkCollectionRepresentation
     */
    public function getDefaultDataCollectionLinkCollection()
    {
        return $this->dataCollectionLinkCollection;
    }

    /**
     * @return Settings
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public static function defaults()
    {
        $meta = new MetaRepresentation();
        $meta->setVisible(true);

        $links = new LinksRepresentation();
        $links->setVisible(false);
        $links->setHasType(false);
        $links->setRepresentationType(SettingsInterface::TYPE_IDENTITY);

        $linked = new LinkedRepresentation();
        $linked->setVisible(false);

        $linkedResource = new LinkedResourceRepresentation();
        $linkedResource->setHasAttributes(true);
        $linkedResource->setHasReference(false);
        $linkedResource->setHasType(false);

        $dataSingle = new DataSingleRepresentation();
        $dataSingle->setRepresentationType(SettingsInterface::TYPE_OBJECT);
        $dataSingle->setHasAttributes(true);
        $dataSingle->setHasReference(true);
        $dataSingle->setHasLinks(true);
        $dataSingle->setHasType(false);

        $dataCollection = new DataCollectionRepresentation();
        $dataCollection->setRepresentationType(SettingsInterface::TYPE_OBJECT);
        $dataCollection->setHasAttributes(true);
        $dataCollection->setHasReference(true);
        $dataCollection->setHasLinks(true);
        $dataCollection->setHasType(false);

        $dataSingleLinkSingle = new DataLinkSingleRepresentation();
        $dataSingleLinkSingle->setRepresentationType(SettingsInterface::TYPE_OBJECT);
        $dataSingleLinkSingle->setHasAttributes(true);
        $dataSingleLinkSingle->setHasReference(false);
        $dataSingleLinkSingle->setHasType(false);

        $dataCollectionLinkSingle = clone $dataSingleLinkSingle;

        $dataSingleLinkCollection = new DataLinkCollectionRepresentation();
        $dataSingleLinkCollection->setRepresentationType(SettingsInterface::TYPE_OBJECT);
        $dataSingleLinkCollection->setHasAttributes(true);
        $dataSingleLinkCollection->setHasReference(false);
        $dataSingleLinkCollection->setHasType(false);

        $dataCollectionLinkCollection = clone $dataSingleLinkCollection;

        return new self(
            $meta,
            $links,
            $linked,
            $linkedResource,
            $dataSingle,
            $dataCollection,
            $dataSingleLinkSingle,
            $dataCollectionLinkSingle,
            $dataSingleLinkCollection,
            $dataCollectionLinkCollection
        );
    }

    /**
     * @param object $resource1
     * @param object $settings
     * @param array  $alter
     * @param array  $properties
     *
     * @return mixed
     */
    private function merge1($resource1, $settings, array $alter, array $properties)
    {
        $result = clone $settings;
        $class1 = get_class($resource1);

        foreach ($properties as $method) {
            if (isset($alter[$class1][$method])) {
                $result->{$method}($alter[$class1][$method]);
            }
        }

        return $result;
    }

    /**
     * @param object $resource1
     * @param object $resource2
     * @param object $settings
     * @param array  $alter
     * @param array  $properties
     *
     * @return mixed
     */
    private function merge2($resource1, $resource2, $settings, array $alter, array $properties)
    {
        $result = clone $settings;
        $class1 = get_class($resource1);
        $class2 = get_class($resource2);

        foreach ($properties as $method) {
            if (isset($alter[$class1][$class2][$method])) {
                $result->{$method}($alter[$class1][$class2][$method]);
            }
        }

        return $result;
    }
}
