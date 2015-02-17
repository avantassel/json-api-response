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

interface SchemaProviderInterface
{
    /**
     * Get resource identity.
     *
     * @param object $resource
     *
     * @return string
     */
    public function getId($resource);

    /**
     * Get resource attributes.
     *
     * @param object $resource
     *
     * @return array
     */
    public function getAttributes($resource);

    /**
     * Get resource references.
     *
     * @param array $identities
     *
     * @return string
     */
    public function getHref(array $identities);

    /**
     * Get resource links.
     *
     * @param object $resource
     *
     * @return array
     */
    public function getLinks($resource);

    /**
     * Get resource type.
     *
     * @return string
     */
    public function getJsonType();

    /**
     * Get URL templates to be added links section of the document.
     *
     * @return array
     */
    public function getUrls();
}
