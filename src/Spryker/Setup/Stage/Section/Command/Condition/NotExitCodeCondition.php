<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Setup\Stage\Section\Command\Condition;

class NotExitCodeCondition extends AbstractCondition
{
    /**
     * @param array $exitCodes
     *
     * @return bool
     */
    public function match(array $exitCodes): bool
    {
        if (!isset($exitCodes[$this->command]) || $exitCodes[$this->command] === $this->exitCode) {
            return false;
        }

        return true;
    }
}
