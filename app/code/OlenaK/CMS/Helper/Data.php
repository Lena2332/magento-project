<?php

declare(strict_types=1);

namespace OlenaK\CMS\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Get Lorem Ipsum text
     *
     * @return string
     */
    public function getLoremIpsumText(): string
    {
        return
            <<<'TEXT'
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cumque delectus maiores repellat!
                Asperiores illo iusto nostrum sequi! Aut commodi, cum dolorem eaque ipsa, ipsum iste quia quisquam sunt
                temporibus voluptates.
                TEXT;
    }
}
