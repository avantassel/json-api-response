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

use \Neomerx\JsonApi\Contracts\ContainerInterface;

class ProviderContainer implements ContainerInterface
{
    /**
     * @var array
     */
    private $providerMapping = [];

    /**
     * @var array
     */
    private $createdProviders = [];

    /**
     * @param array $providers
     */
    public function __construct(array $providers = [])
    {
        $this->registerArray($providers);
    }

    /**
     * @param string $resourceType
     * @param string $providerClass
     */
    public function register($resourceType, $providerClass)
    {
        assert('is_string($resourceType) and !empty($resourceType)');
        assert('is_string($providerClass) and !empty($providerClass)');
        assert('isset($this->providerMapping[$resourceType]) === false');

        $this->providerMapping[$resourceType] = $providerClass;
    }

    /**
     * @param array $providers
     */
    public function registerArray(array $providers)
    {
        foreach ($providers as $type => $className) {
            $this->register($type, $className);
        }
    }

    /**
     * @inheritdoc
     */
    public function getProvider($resource)
    {
        $resourceType = get_class($resource);

        if (isset($this->createdProviders[$resourceType])) {
            return $this->createdProviders[$resourceType];
        }

        $className = $this->providerMapping[$resourceType];
        $this->createdProviders[$resourceType] = ($provider = new $className);

        return $provider;
    }
}
