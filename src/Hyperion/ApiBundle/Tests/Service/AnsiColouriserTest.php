<?php
namespace Hyperion\ApiBundle\Tests\Service;

use Hyperion\ApiBundle\Service\AnsiColouriser;

class AnsiColouriserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @small
     * @dataProvider codeProvider
     */
    public function testGetCodes($code, $expected)
    {
        $parser = new AnsiColouriser();

        $rc     = new \ReflectionClass($parser);
        $method = $rc->getMethod('getCodes');
        $method->setAccessible(true);

        $codes = $method->invoke($parser, $code);

        $this->assertTrue(is_array($codes));
        $this->assertCount(count($expected) + 1, $codes);
        $this->assertEquals(strlen($code), $codes[0]);

        foreach ($expected as $index => $val) {
            $this->assertSame($val, $codes[$index + 1]);
        }
    }

    public function codeProvider()
    {
        return [
            ['1;44;23m', [1, 44, 23]],
            ['0;12m', [0, 12]],
        ];
    }


    /**
     * @small
     * @dataProvider parseProvider
     */
    public function testParse($in, $out)
    {
        $parser = new AnsiColouriser();

        $html = $parser->parse($in);
        $this->assertEquals($out, $html);
    }

    public function parseProvider()
    {
        return [
            ["Hello ".chr(27).'[32mWorld', 'Hello <span style="color: rgb(0,205,0)">World</span>'],
        ];
    }


}
 