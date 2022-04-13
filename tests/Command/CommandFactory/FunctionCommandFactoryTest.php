<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\FormulaInterpreter\Command\CommandFactory;

use FormulaInterpreter\Command\CommandFactory\CommandFactoryException;
use FormulaInterpreter\Command\FunctionCommand;
use FormulaInterpreter\Command\CommandInterface;
use FormulaInterpreter\Command\CommandFactory\FunctionCommandFactory;
use FormulaInterpreter\Exception\UnknownFunctionException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * Description of NumericCommandFactory
 *
 * @author mathieu
 */
class FunctionCommandFactoryTest extends TestCase
{
    public function setUp(): void
    {
        $this->argumentCommandFactory = $this->createMock(
            'FormulaInterpreter\Command\CommandFactory\CommandFactoryInterface'
        );
        $this->factory = new FunctionCommandFactory($this->argumentCommandFactory);
        $this->piFunction = function () {
            return 3.14;
        };
        $this->factory->registerFunction('pi', $this->piFunction);
    }

    public function testCreateShouldReturnFunctionCommand()
    {
        $options = ['name' => 'pi'];
        $object = $this->factory->create($options);
        $this->assertTrue($object instanceof FunctionCommand, 'An instance of FunctionCommand should be returned');
    }

    public function testCreateWithNoArguments()
    {
        $options = ['name' => 'pi'];
        $object = $this->factory->create($options);
        Assert::assertObjectHasAttribute('callable', $object);
        Assert::assertEquals($object->getCallable(), $this->piFunction);
        Assert::assertEquals($object->getArgumentCommands(), []);
//        $this->assertObjectPropertyEquals($object, 'callable', $this->piFunction);
//        $this->assertObjectPropertyEquals($object, 'argumentCommands', []);
    }

    public function testCreateWithArguments()
    {
        $argumentCommand = $this->createMock(
            'FormulaInterpreter\Command\CommandInterface'
        );
        $this->argumentCommandFactory->expects($this->once())
                ->method('create')
                ->with($this->equalTo(['type' => 'fake']))
                ->will($this->returnValue($argumentCommand));

        $options = [
            'name' => 'pi',
            'arguments' => [['type' => 'fake']]
        ];
        $object = $this->factory->create($options);

        Assert::assertEquals($object->getCallable(), $this->piFunction);
        Assert::assertEquals($object->getArgumentCommands(), [$argumentCommand]);
    }

    public function testCreateWithNotExistingFunction()
    {
        $this->expectException(UnknownFunctionException::class);
        $options = [
            'name' => 'notExistingFunction',
        ];
        $this->factory->create($options);
    }

    public function testCreateWithMissingNameOption()
    {
        $this->expectException(CommandFactoryException::class);
        $this->factory->create([]);
    }
}
