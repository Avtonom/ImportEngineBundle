<?php
namespace Mathielen\ImportEngineBundle\Tests\DependencyInjection;

use Mathielen\ImportEngineBundle\DependencyInjection\MathielenImportEngineExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class CompareTest extends \PHPUnit_Framework_TestCase
{

    private function getBaseContainer()
    {
        $extension = new MathielenImportEngineExtension();
        $container = new ContainerBuilder();
        $container->registerExtension($extension);
        $container->register('event_dispatcher', $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface'));
        $container->register('import_service', new MyDummyService()); //target service
        $container->register('jms_serializer', $this->getMock('JMS\Serializer\SerializerInterface'));
        $container->register('validator', $this->getMock('Symfony\Component\Validator\ValidatorInterface'));
        $container->register('doctrine.orm.entity_manager', $this->getMock('Doctrine\ORM\EntityManagerInterface'));

        return $container;
    }

    private function getXmlDefinitions($filename)
    {
        $container = $this->getBaseContainer();
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/Fixtures/Xml/'));
        $loader->load("$filename.xml");

        $container->loadFromExtension('mathielen_import_engine');
        $container->compile();

        return $container->getDefinitions();
    }

    private function getYamlDefinitions($filename)
    {
        $container = $this->getBaseContainer();
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Fixtures/Yaml/'));
        $loader->load("$filename.yml");

        $container->loadFromExtension('mathielen_import_engine');
        $container->compile();

        return $container->getDefinitions();
    }

    public function testFullXmlAndYamlSame()
    {
        $this->assertEquals($this->getYamlDefinitions('full'), $this->getXmlDefinitions('full'));
        $this->assertEquals($this->getYamlDefinitions('minimum'), $this->getXmlDefinitions('minimum'));
    }

}

class MyDummyService
{
}
