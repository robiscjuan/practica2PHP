<?php// tests/Functional/ResultApiTest.php
namespace MiW16\Results\Tests\Functional;
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
        $users = $this->resultRepository->findAll();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(count($body->users), count($users));
    }

    public function testGet200()
    {
        $userCreated = new User();
        $userCreated->setUsername("user" . rand(0, 1000000));
        $userCreated->setEmail("user" . rand(0, 1000000) . "@mail.com");
        $userCreated->setPassword("1234");
        $userCreated->setEnabled(true);

        $this->entityManager->persist($userCreated);
        $this->entityManager->flush();
        $this->entityManager->refresh($userCreated);

        $response = $this->runApp('GET', '/results/' . $userCreated->getId());

        $user = json_decode($response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($user->username, $userCreated->getUsername());

        $this->entityManager->remove($userCreated);
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
        $user = json_decode($response->getBody());
        $userCreated = $this->resultRepository->findOneById($user->id);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($user->username, $userCreated->getUsername());

        $this->entityManager->remove($userCreated);
        $this->entityManager->flush();
    }

    public function testPost422()
    {
        $response = $this->runApp('POST', '/results', $this->generateIncompleteUser());
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testPost400()
    {
        $userCreated = new User();
        $userCreated->setUsername("user" . rand(0, 1000000));
        $userCreated->setEmail("user" . rand(0, 1000000) . "@mail.com");
        $userCreated->setPassword("1234");
        $userCreated->setEnabled(true);

        $this->entityManager->persist($userCreated);
        $this->entityManager->flush();
        $this->entityManager->refresh($userCreated);
        $data = [
            'username' => $userCreated->getUsername(),
            'email' => $userCreated->getEmail(),
            'enabled' => true,
            'password' => 'abc123'
        ];
        $response = $this->runApp('POST', '/results', $data);
        $body = json_decode($response->getBody());
        $this->assertEquals(400, $response->getStatusCode());
        $this->entityManager->remove($userCreated);
        $this->entityManager->flush();
    }

    public function testPut200()
    {
        $userCreated = new User();
        $userCreated->setUsername("user" . rand(0, 1000000));
        $userCreated->setEmail("user" . rand(0, 1000000) . "@mail.com");
        $userCreated->setPassword("1234");
        $userCreated->setEnabled(true);
        $this->entityManager->persist($userCreated);
        $this->entityManager->flush();
        $this->entityManager->refresh($userCreated);
        $content = [
            'username' => 'user' . rand(0, 1000000),
            'email' => 'user' . rand(0, 1000000) . '@mail.com'
        ];
        $response = $this->runApp('PUT', '/results/' . $userCreated->getId(), $content);
        $user = json_decode($response->getBody());
        $this->entityManager->refresh($userCreated);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($user->username, $userCreated->getUsername());
    }

    public function testPut404()
    {
        $content = [
            'username' => 'user' . rand(0, 1000000),
            'email' => 'user' . rand(0, 1000000) . '@mail.com'
        ];

        $response = $this->runApp('PUT', '/results/0', $content);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testPut400()
    {
        $userCreated = $this->createUser();

        $this->entityManager->persist($userCreated);
        $this->entityManager->flush();
        $this->entityManager->refresh($userCreated);

        $data = [
            'username' => $userCreated->getUsername(),
            'email' => $userCreated->getEmail()
        ];

        $response = $this->runApp('PUT', '/results/' . $userCreated->getId(), $data);
        $this->assertEquals(400, $response->getStatusCode());

        $this->entityManager->remove($userCreated);
        $this->entityManager->flush();
    }

    public function testDelete204()
    {
        $userCreated = $this->createUser();

        $this->entityManager->persist($userCreated);
        $this->entityManager->flush();
        $this->entityManager->refresh($userCreated);

        $response = $this->runApp('DELETE', '/results/' . $userCreated->getId());
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

    private function createUser(){
        $userCreated = new User();
        $userCreated->setUsername('test' . mt_rand(0, 999999));
        $userCreated->setEmail(mt_rand(0, 999999) . '@test.com');
        $userCreated->setEnabled(true);
        $userCreated->setPassword("abc123");
        return $userCreated;
    }
    private function generateOKResult(){
        return [
            'result' => mt_rand(0, 999999),
            'user' => $this->user->getId(),
            'time' => new \DateTime()
        ];
    }
    private function generateIncompleteUser(){
        return [
            'enabled' => true,
            'password' => 'abc123'
        ];
    }
}