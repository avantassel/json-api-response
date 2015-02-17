<?php namespace Neomerx\JsonApi\Contracts;

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

use \Neomerx\JsonApi\Settings\MetaRepresentation;
use \Neomerx\JsonApi\Settings\LinksRepresentation;
use \Neomerx\JsonApi\Settings\LinkedRepresentation;
use \Neomerx\JsonApi\Settings\DataSingleRepresentation;
use \Neomerx\JsonApi\Settings\DataCollectionRepresentation;
use \Neomerx\JsonApi\Settings\DataLinkSingleRepresentation;
use \Neomerx\JsonApi\Settings\LinkedResourceRepresentation;
use \Neomerx\JsonApi\Settings\DataLinkCollectionRepresentation;

interface SettingsInterface
{
    const TYPE_OBJECT   = 0;
    const TYPE_IDENTITY = 1;
    const TYPE_COMBINED = 2; // for collections only. show collection as a single object.

    /**
     * @param object $resource
     *
     * @return MetaRepresentation
     */
    public function meta($resource);

    /**
     * @param object $resource
     *
     * @return LinksRepresentation
     */
    public function links($resource);

    /**
     * @param object $resource
     *
     * @return LinkedRepresentation
     */
    public function linked($resource);

    /**
     * @param object $resource
     * @param object $linkedResource
     *
     * @return LinkedResourceRepresentation
     */
    public function linkedResource($resource, $linkedResource);

    /**
     * @param object $resource
     *
     * @return DataSingleRepresentation
     */
    public function dataSingle($resource);

    /**
     * @param object $resource
     *
     * @return DataCollectionRepresentation
     */
    public function dataCollection($resource);

    /**
     * @param object $resource
     * @param object $linkResource
     *
     * @return DataLinkSingleRepresentation
     */
    public function dataSingleLinkSingle($resource, $linkResource);

    /**
     * @param object $resource
     * @param object $linkResource
     *
     * @return DataLinkSingleRepresentation
     */
    public function dataCollectionLinkSingle($resource, $linkResource);

    /**
     * @param object $resource
     * @param object $linkResource
     *
     * @return DataLinkCollectionRepresentation
     */
    public function dataSingleLinkCollection($resource, $linkResource);

    /**
     * @param object $resource
     * @param object $linkResource
     *
     * @return DataLinkCollectionRepresentation
     */
    public function dataCollectionLinkCollection($resource, $linkResource);
}
