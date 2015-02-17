<?php namespace Neomerx\JsonApi\Schema;

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

use \Neomerx\JsonApi\Contracts\SchemaProviderInterface;

abstract class ProviderBase implements SchemaProviderInterface
{
    /**
     * @var string
     */
    private $identityProperty;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var array
     */
    private $urlTemplates;

    /**
     * @param string $identityProperty Name of the property with resource identity (e.g. 'id')
     * @param string $url              URL of the resources
     * @param string $type             Resource type
     * @param string $baseUrl          Gets URL prefix that is added to all URLs (e.g. links, references, etc)
     * @param array  $urlTemplates     Additional URLs that cannot be received from schema directly.
     *
     * For example for `Post` object might have an additional link that will fetch the comments for it.
     * {
     *    "links": {
     *      "posts.comments": "http://example.com/comments?posts={posts.id}"
     *    },
     *    "posts":
     *    [
     *      ...
     *    ]
     * }
     */
    public function __construct($identityProperty, $url, $type, $baseUrl = '', array $urlTemplates = [])
    {
        $this->setUrl($url);
        $this->setJsonType($type);
        $this->setBaseUrl($baseUrl);
        $this->setIdentityProperty($identityProperty);
        $this->setUrlTemplates($urlTemplates);
    }

    /**
     * @inheritdoc
     */
    public function getId($resource)
    {
        assert('is_object($resource) === true');
        return $resource->{$this->identityProperty};
    }

    /**
     * @inheritdoc
     */
    public function getHref(array $identities)
    {
        return $this->composeUrl($this->baseUrl, $this->url).implode(',', $identities);
    }

    /**
     * @inheritdoc
     */
    public function getJsonType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     *
     * @see http://jsonapi.org/format/#document-structure-url-templates
     */
    public function getUrls()
    {
        $urlBase = $this->baseUrl;
        $urls    = $this->urlTemplates;

        assert('is_string($urlBase)');
        assert('is_array($urls)');

        $gotUrlBase = (empty($urlBase) === false);
        foreach ($urls as $linkProperty => $urlInfo) {
            if (is_array($urlInfo) === true) {
                assert('count($urlInfo) === 2');
                list($url, $jsonType) = $urlInfo;
            } else {
                assert('is_string($urlInfo) === true');
                $url      = $urlInfo;
                $jsonType = $linkProperty;
            }
            $urls[$linkProperty] = [
                $gotUrlBase === true ? $this->composeUrl($urlBase, $url) : $url,
                $jsonType
            ];
        }

        return $urls;
    }

    /**
     * @return string
     */
    protected function getIdentityProperty()
    {
        return $this->identityProperty;
    }

    /**
     * @param string $identityProperty
     *
     * @return void
     */
    protected function setIdentityProperty($identityProperty)
    {
        assert('is_string($identityProperty) === true && empty($identityProperty) === false');
        $this->identityProperty = $identityProperty;
    }

    /**
     * @return string
     */
    protected function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return void
     */
    protected function setUrl($url)
    {
        assert('is_string($url) === true && empty($url) === false');
        $this->url = $url;
    }

    /**
     * @param string $type
     *
     * @return void
     */
    protected function setJsonType($type)
    {
        assert('is_string($type) === true && empty($type) === false');
        $this->type = $type;
    }

    /**
     * @return string
     */
    protected function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     *
     * @return void
     */
    protected function setBaseUrl($baseUrl)
    {
        assert('is_string($baseUrl) === true');
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return array
     */
    protected function getUrlTemplates()
    {
        return $this->urlTemplates;
    }

    /**
     * @param array $urlTemplates
     *
     * @return void
     */
    protected function setUrlTemplates(array $urlTemplates)
    {
        $this->urlTemplates = $urlTemplates;
    }

    /**
     * @param string $baseUrl
     * @param string $tail
     *
     * @return string
     */
    protected function composeUrl($baseUrl, $tail)
    {
        assert('is_string($baseUrl) && is_string($tail)');

        if (empty($baseUrl) === false && empty($tail) === false) {
            $baseUrl = rtrim($baseUrl, '/');
            if ($tail[0] !== '/') {
                $tail = '/'.$tail;
            }
        }

        return $baseUrl.$tail;
    }
}
