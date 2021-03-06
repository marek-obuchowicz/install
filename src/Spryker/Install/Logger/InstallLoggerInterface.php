<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Install\Logger;

interface InstallLoggerInterface
{
    /**
     * @param string|array $message
     *
     * @return void
     */
    public function log($message);
}
