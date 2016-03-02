<?php

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
    public function getName()
    {
        return 'integer_map';
    }

    /**
     * @return string[]
     */
    public function getValues()
    {
        return [
            self::MAPPED_VALUE => 'Mapped value description.',
        ];
    }
}
