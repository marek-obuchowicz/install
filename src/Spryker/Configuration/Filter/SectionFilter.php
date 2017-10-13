<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Configuration\Filter;

class SectionFilter implements FilterInterface
{
    const EXCLUDED = 'excluded';

    /**
     * @var array
     */
    protected $includeExcluded;

    /**
     * @var array
     */
    protected $sectionsToBeExecuted;

    /**
     * @var array
     */
    protected $groupsToBeExecuted;

    /**
     * @var array
     */
    protected $excludedSections;

    /**
     * @param array $includeExcluded
     * @param array $sectionsToBeExecuted
     * @param array $groupsToBeExecuted
     * @param array $excludedStages
     */
    public function __construct(array $includeExcluded, array $sectionsToBeExecuted, array $groupsToBeExecuted, array $excludedStages)
    {
        $this->includeExcluded = $includeExcluded;
        $this->sectionsToBeExecuted = $sectionsToBeExecuted;
        $this->groupsToBeExecuted = $groupsToBeExecuted;
        $this->excludedSections = $excludedStages;
    }

    /**
     * @param array $items
     *
     * @return array
     */
    public function filter(array $items)
    {
        $filtered = [];

        foreach ($items as $sectionName => $sectionDefinition) {
            if ($this->shouldSectionBeAdded($sectionName, $sectionDefinition)) {
                $filtered[$sectionName] = $sectionDefinition;
            }
        }

        return $filtered;
    }

    /**
     * @param string $sectionName
     * @param array $sectionDefinition
     *
     * @return bool
     */
    protected function shouldSectionBeAdded($sectionName, array $sectionDefinition)
    {
        if ($this->containsRequestedGroup($sectionDefinition)) {
            return true;
        }

        if ($this->onlyRunSelected()) {
            return $this->isSectionRequested($sectionName);
        }

        if (!$this->isSectionExcluded($sectionName, $sectionDefinition)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function onlyRunSelected()
    {
        return (count($this->sectionsToBeExecuted) > 0);
    }

    /**
     * @param array $sectionDefinition
     *
     * @return bool
     */
    protected function containsRequestedGroup(array $sectionDefinition)
    {
        foreach ($this->filterForRequestedGroupCheck($sectionDefinition) as $commandDefinition) {
            if (array_intersect($commandDefinition['groups'], $this->groupsToBeExecuted)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $sectionDefinition
     *
     * @return array
     */
    protected function filterForRequestedGroupCheck(array $sectionDefinition)
    {
        $filtered = [];

        foreach ($sectionDefinition as $commandName => $commandDefinition) {
            if ($commandName !== static::EXCLUDED && isset($commandDefinition['groups'])) {
                $filtered[$commandName] = $commandDefinition;
            }
        }

        return $filtered;
    }

    /**
     * @param string $sectionName
     *
     * @return bool
     */
    protected function isSectionRequested($sectionName)
    {
        return in_array($sectionName, $this->sectionsToBeExecuted);
    }

    /**
     * @param string $sectionName
     * @param array $sectionDefinition
     *
     * @return bool
     */
    protected function isSectionExcluded($sectionName, array $sectionDefinition)
    {
        if ($this->isExcludedByName($sectionName)) {
            return true;
        }

        if ($this->isExcludedByDefinition($sectionDefinition) && !$this->shouldBeIncluded($sectionName)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $sectionName
     *
     * @return bool
     */
    protected function shouldBeIncluded($sectionName)
    {
        return (count($this->includeExcluded) > 0 && in_array($sectionName, $this->includeExcluded));
    }

    /**
     * @param string $sectionName
     *
     * @return bool
     */
    protected function isExcludedByName($sectionName)
    {
        return count($this->excludedSections) > 0 && in_array($sectionName, $this->excludedSections);
    }

    /**
     * @param array $sectionDefinition
     *
     * @return bool
     */
    protected function isExcludedByDefinition(array $sectionDefinition)
    {
        return (isset($sectionDefinition[static::EXCLUDED])) ? $sectionDefinition[static::EXCLUDED] : false;
    }
}
