<?php // tests/Functional/ResultApiTest.php
namespace MiW16\Results\Tests\Functional;

use MiW16\Results\Entity\Result;
use MiW16\Results\Entity\User;

require_once '../../bootstrap.php';
require_once 'BaseTestCase.php';

/**
 * Class UserApiTest
 * @package MiW16\Results\Tests\Functional
 */
class ResultApiTest extends BaseTestCase
{

    private $resultRepository;
    private $entityManager;

    protected function setUp()
    {
        $this->entityManager = getEntityManager();
        $this->userRepository = $this->entityManager->getRepository('MiW16\Results\Entity\User');
        $this->resultRepository = $this->entityManager->getRepository('MiW16\Results\Entity\Result');

        $this->user = $this->createUser();

        $this->entityManager->persist($this->user);
        $this->entityManager->flush();
        $this->entityManager->refresh($this->user);
    }

    public function testCGet200()
    {
        $response = $this->runApp('GET', '/results');
        $body = json_decode($response->getBody());
        $results = $this->resultRepository->findAll();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(count($body->results), count($results));
    }

    public function testGet200()
    {
        $resultCreated = $this->createNewResult();

        $this->entityManager->persist($resultCreated);
        $this->entityManager->flush();
        $this->entityManager->refresh($resultCreated);

        $response = $this->runApp('GET', '/results/' . $resultCreated->getId());

        $result = json_decode($response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($result->id, $resultCreated->getId());

        $this->entityManager->remove($resultCreated);
        $this->entityManager->flush();
    }

    public function testGet404()
    {
        $response = $this->runApp('GET', '/results/0');
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testPost200()
    {
        $response = $this->runApp('POST', '/results', $this->generateOKResult());
        $result = json_decode($response->getBody());
        $resultCreated = $this->resultRepository->findOneById($result->id);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($result->id, $resultCreated->getId());

        $this->entityManager->remove($resultCreated);
        $this->entityManager->flush();
    }

    public function testPost422()
    {
        $response = $this->runApp('POST', '/results', $this->generateIncompleteResult());
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testPost400()
    {
        $data = [
            'result' => mt_rand(0, 999999),
            'user_id' => 0,
            'time' => new \DateTime()
        ];

        $response = $this->runApp('POST', '/results', $data);
        $this->assertEquals(400, $response->getStatusCode());

        $this->entityManager->flush();
    }

    public function testPut200()
    {
        $resultCreated = $this->createNewResult();

        $this->entityManager->persist($resultCreated);
        $this->entityManager->flush();
        $this->entityManager->refresh($resultCreated);

        $data = [
            'result' => 1212,
        ];
        $response = $this->runApp('PUT', '/results/' . $resultCreated->getId(), $data);
        $result = json_decode($response->getBody());
        $this->entityManager->refresh($resultCreated);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($result->result, $resultCreated->getResult());
    }

    public function testPut404()
    {
        $data = [
            'result' => 1212,
        ];

        $response = $this->runApp('PUT', '/results/0', $data);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testPut400()
    {
        $resultCreated = $this->createNewResult();

        $this->entityManager->persist($resultCreated);
        $this->entityManager->flush();
        $this->entityManager->refresh($resultCreated);

        $data = [
            'user_id' => -1,
        ];

        $response = $this->runApp('PUT', '/results/' . $resultCreated->getId(), $data);
        $this->assertEquals(400, $response->getStatusCode());

        $this->entityManager->remove($resultCreated);
        $this->entityManager->flush();
    }

    public function testDelete204()
    {
        $resultCreated = $this->createNewResult();

        $this->entityManager->persist($resultCreated);
        $this->entityManager->flush();
        $this->entityManager->refresh($resultCreated);

        $response = $this->runApp('DELETE', '/results/' . $resultCreated->getId());
        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testDelete404()
    {
        $response = $this->runApp('DELETE', '/results/0');
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function tearDown()
    {
        unset($this->entityManager);
        unset($this->resultRepository);
    }

    private function createUser()
    {
        $resultCreated = new User();
        $resultCreated->setUsername('test' . mt_rand(0, 999999));
        $resultCreated->setEmail(mt_rand(0, 999999) . '@test.com');
        $resultCreated->setEnabled(true);
        $resultCreated->setPassword("abc123");
        return $resultCreated;
    }

    private function createNewResult()
    {
        $resultCreated = new Result(mt_rand(0, 999999), $this->user, new \DateTime());
        return $resultCreated;
    }

    private function generateOKResult()
    {
        return [
            'result' => mt_rand(0, 999999),
            'user_id' => $this->user->getId(),
            'time' => new \DateTime()
        ];
    }

    private function generateIncompleteResult()
    {
        return [
            'user_id' => $this->user->getId(),
            'time' => new \DateTime()
        ];
    }
}