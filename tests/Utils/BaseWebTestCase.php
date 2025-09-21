<?php

declare(strict_types=1);

namespace App\Tests\Utils;

use App\Tests\Utils\Attribute\Fixtures;
use App\Tests\Utils\Trait\DataFormattingTestTools;
use App\Tests\Utils\Trait\ListAssertions;
use App\Tests\Utils\Trait\RequestTestTools;
use App\Tests\Utils\Trait\ResponseAssertions;
use App\Tests\Utils\Trait\ResponseTestTools;
use App\Tests\Utils\Trait\ValidationAssertions;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Psr\Container\ContainerInterface;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BaseWebTestCase extends WebTestCase 
{
    use RequestTestTools, ResponseTestTools, DataFormattingTestTools; 
    use ResponseAssertions, ValidationAssertions, ListAssertions;

    protected KernelBrowser $client;
    protected ContainerInterface $container;
    protected AbstractDatabaseTool $dbTool;
    protected NormalizerInterface $normalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->container = $this->getContainer();
        $this->dbTool = $this->container->get(DatabaseToolCollection::class)->get();
        $this->normalizer = $this->container->get(NormalizerInterface::class);

        $this->loadFixtures();
    }

    protected function getFixturesAttribute(): ?Fixtures
    {
        $fixturesAttributes = (new ReflectionMethod($this, $this->name()))->getAttributes(Fixtures::class);
        if(empty($fixturesAttributes)){
            return null;
        }

        return $fixturesAttributes[0]->newInstance();
    }

    protected function loadFixtures(): void
    {
        $fixturesAttribute = $this->getFixturesAttribute();
        if($fixturesAttribute){
            $this->dbTool->loadFixtures($fixturesAttribute->fixtures);
        }
    }
}