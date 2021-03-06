<?php namespace Neomerx\JsonApi\Settings\Traits;

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

trait ResourceRepresentationTypeTrait
{
    /**
     * @var int
     */
    private $representationType;

    /**
     * Get representation type.
     *
     * See SettingsInterface::TYPE_XXX for possible values.
     *
     * @return int
     */
    public function getRepresentationType()
    {
        return $this->representationType;
    }

    /**
     * Set representation type.
     *
     * See SettingsInterface::TYPE_XXX for possible values.
     *
     * @param int $representationType
     */
    public function setRepresentationType($representationType)
    {
        settype($representationType, 'int');
        assert(
            'in_array($representationType, ['.
            SettingsInterface::TYPE_OBJECT.', '.
            SettingsInterface::TYPE_IDENTITY.'])'
        );
        $this->representationType = $representationType;
    }
}
