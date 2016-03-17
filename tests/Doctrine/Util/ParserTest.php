<?php

namespace OctoLab\Common\Doctrine\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider sqlQueryProvider
     *
     * @param string $text
     * @param string[] $expected
     */
    public function extractSql($text, array $expected)
    {
        self::assertEquals($expected, Parser::extractSql($text));
    }

    /**
     * @return array[]
     */
    public function sqlQueryProvider()
    {
        return [
            [
                '-- комментарий
                INSERT INTO a (b, c)
                VALUES (1, 2);
                # комментарий',
                ['INSERT INTO a (b, c) VALUES (1, 2)']
            ],
            [
                '/* комментарий */
                /*
                 * комментарий
                 */
                UPDATE
                a,
                (SELECT b FROM c WHERE d=1) e
                SET
                    f=1 # комментарий
                WHERE
                    g=2;
                /*
                 комментарий
                 */
                UPDATE
                h,
                (SELECT i FROM j WHERE k=1) l
                SET
                    m=1 -- комментарий
                WHERE
                    n=2;',
                [
                    'UPDATE a, (SELECT b FROM c WHERE d=1) e SET f=1 WHERE g=2',
                    'UPDATE h, (SELECT i FROM j WHERE k=1) l SET m=1 WHERE n=2',
                ]
            ],
            ['', []],
        ];
    }
}
