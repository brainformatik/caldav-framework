<?php
/**
 * (c) Brainformatik GmbH [info@brainformatik.com]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainformatik\CalDAV\Helper;

use PHPUnit\Framework\TestCase;

/**
 * BaseTestCase
 */
abstract class BaseTestCase extends TestCase {

    /**
     * @var array
     */
    protected $backupGlobalsBlacklist = ['GLOBALS'];

    /**
     * Enable or disable the backup and restoration of globals
     * @var boolean
     */
    protected $backupGlobals = false;

    /**
     * Enable or disable the backup and restoration of static attributes.
     * @var boolean
     */
    protected $backupStaticAttributes = false;

    /**
     * @return void
     */
    public function runBare() {
        \PHPUnit_Framework_Error_Warning::$enabled = false;
        parent::runBare();
    }

    /**
     * Call protected/private method of a class
     *
     * @param  object $object
     * @param  string $methodName
     * @param  array  $parameters
     * @return mixed
     */
    protected function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Call protected/private property of a class
     *
     * @param  object $object
     * @param  string $propertyName
     * @return mixed
     */
    protected function invokeProperty(&$object, $propertyName)
    {
        $closure = function () use ($propertyName) {
            return $this->{$propertyName};
        };
        $fn = $closure->bindTo($object, $object);

        return $fn();
    }

    /**
     * Returns a mock object which allows for calling protected methods / properties.
     *
     * @param string $originalClassName
     * @param array  $methods
     * @param array  $arguments
     * @param string $mockClassName
     * @param bool   $callOriginalConstructor
     * @param bool   $callOriginalClone
     * @param bool   $callAutoload
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAccessibleMock($originalClassName, $methods = [], array $arguments = [], $mockClassName = '', $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true) {
        return $this->getMock($this->buildAccessibleProxy($originalClassName), $methods, $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload);
    }

    /**
     * Returns a mock object which allows for calling protected methods / properties
     *
     * @param string $originalClassName
     * @param array  $arguments
     * @param string $mockClassName
     * @param bool   $callOriginalConstructor
     * @param bool   $callOriginalClone
     * @param bool   $callAutoload
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAccessibleMockForAbstractClass($originalClassName, array $arguments = [], $mockClassName = '', $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true) {
        return $this->getMockForAbstractClass($this->buildAccessibleProxy($originalClassName), $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload);
    }

    /**
     * Injects {dependency} into property {name} of {target}
     *
     * @param object $target
     * @param string $name
     * @param object $dependency
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    protected function inject($target, $name, $dependency) {
        if (!is_object($target)) {
            throw new \InvalidArgumentException('Wrong type for argument $target, must be object.');
        }

        $objectReflection = new \ReflectionObject($target);
        $methodNamePart = strtoupper($name[0]) . substr($name, 1);
        if ($objectReflection->hasMethod('set' . $methodNamePart)) {
            $methodName = 'set' . $methodNamePart;
            $target->$methodName($dependency);
        } elseif ($objectReflection->hasMethod('inject' . $methodNamePart)) {
            $methodName = 'inject' . $methodNamePart;
            $target->$methodName($dependency);
        } elseif ($objectReflection->hasProperty($name)) {
            $property = $objectReflection->getProperty($name);
            $property->setAccessible(true);
            $property->setValue($target, $dependency);
        } else {
            throw new \RuntimeException('Could not inject ' . $name . ' into object of type ' . get_class($target));
        }
    }

    /**
     * Test multiple exception that are thrown in one method
     *
     * @param callable $callback
     * @param string   $expectedException
     * @param null     $expectedCode
     * @param null     $expectedMessage
     */
    protected function assertException(callable $callback, $expectedException = 'Exception', $expectedCode = null, $expectedMessage = null) {
        $expectedException = ltrim((string) $expectedException, '\\');
        if (!class_exists($expectedException)) {
            $this->fail(
                sprintf('This exception "%s" does not exist.', $expectedException)
            );
        }

        try {
            $callback();
        } catch (\Throwable $e) {
            $className = get_class($e);

            $this->assertInstanceOf($expectedException, $e);

            if (null !== $expectedCode) {
                $this->assertEquals($expectedCode, $e->getCode(), sprintf('Failed asserting code of thrown %s.', $className));
            }
            if (null !== $expectedMessage) {
                $this->assertContains($expectedMessage, $e->getMessage(), sprintf('Failed asserting the message of thrown %s.', $className));
            }

            return;
        }

        $this->fail(
            sprintf('This exception "%s" is failed to assert.', $expectedException)
        );
    }
}