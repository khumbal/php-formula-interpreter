<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\FormulaInterpreter\Parser;

use FormulaInterpreter\Parser\NumericParser;
use FormulaInterpreter\Parser\ParserException;

/**
 * Description of NumericParserTest
 *
 * @author mathieu
 */
class NumericParserTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->parser = new NumericParser();
    }
    
    /**
     * @dataProvider getIntegerValue
     */
    public function testParseInteger($expression, $infos)
    {
        $infos['type'] = 'numeric';
        $this->assertEquals($this->parser->parse($expression), $infos);
    }
    
    public function getIntegerValue()
    {
        return [
            ['2', ['value' => 2]],
            ['2.4', ['value' => 2.4]],
            [' 2.4 ', ['value' => 2.4]],
        ];
    }
    
    /**
     * @dataProvider getUncorrectExpressionData
     */
    public function testParseUncorrectExpression($expression)
    {
        $this->expectException(ParserException::class);
        $this->parser->parse($expression);
    }
    
    public function getUncorrectExpressionData()
    {
        return [
            ['mlksdf'],
            ['MLKmlm'],
            [' MLKmlm '],
            [' some_function( '],
            ['2.23.23']
        ];
    }
}
