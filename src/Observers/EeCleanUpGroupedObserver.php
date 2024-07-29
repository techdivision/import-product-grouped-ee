<?php
/**
 * Copyright (c) 2024 TechDivision GmbH <info@techdivision.com> - TechDivision GmbH
 * All rights reserved
 *
 * This product includes proprietary software developed at TechDivision GmbH, Germany
 * For more information see https://www.techdivision.com
 *
 * To obtain a valid license for using this software, please contact us at
 * license@techdivision.com
 */
declare(strict_types=1);

namespace TechDivision\Import\Product\Grouped\Ee\Observers;

use TechDivision\Import\Product\Grouped\Observers\CleanUpGroupedProductRelationObserver;

/**
 * @copyright Copyright (c) 2024 TechDivision GmbH <info@techdivision.com> - TechDivision GmbH
 * @link http://www.techdivision.com
 * @author MET <met@techdivision.com>
 */
class EeCleanUpGroupedObserver extends CleanUpGroupedProductRelationObserver
{
    /**
     * Return's the PK to create the grouped product => child relation.
     *
     * @return int The PK to create the relation with
     */
    protected function getLastPrimaryKey(): int
    {
        return $this->getSubject()->getLastRowId();
    }
}
