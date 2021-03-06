<?php

declare(strict_types = 1);

namespace OctoLab\Common\Doctrine\Type;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class IntegerMapTypeMock extends IntegerMapType
{
    const MAPPED_VALUE = 1;

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'integer_map';
    }

    /**
     * @return string[]
     */
    public function getValues(): array
    {
        return [
            self::MAPPED_VALUE => 'Mapped value description.',
        ];
    }
}
