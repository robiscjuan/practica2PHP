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
    private $userRepository;
    private $entityManager;

    protected function setUp()
    {
        $this->entityManager = getEntityManager();
        $this->userRepository = $this->entityManager->getRepository('MiW16\Results\Entity\User');
    }

    public function testCGet200()
    {
        $response = $this->runApp('GET', '/users');
        $body = json_decode($response->getBody());
        $dbUsers = $this->userRepository->findAll();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(count($body->users), count($dbUsers));
    }

    public function testGet200()
    {
        $dbUser = new User();
        $dbUser->setUsername("user" . rand(0, 1000000));
        $dbUser->setEmail("user" . rand(0, 1000000) . "@mail.com");
        $dbUser->setPassword("1234");
        $dbUser->setEnabled(true);
        $this->entityManager->persist($dbUser);
        $this->entityManager->flush();
        $this->entityManager->refresh($dbUser);
        $response = $this->runApp('GET', '/users/' . $dbUser->getId());
        $user = json_decode($response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($user->username, $dbUser->getUsername());
        $this->entityManager->remove($dbUser);
        $this->entityManager->flush();
    }

    public function testGet404()
    {
        $response = $this->runApp('GET', '/users/9999999999999');
        $body = json_decode($response->getBody());
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("User not found", $body->message);
    }

    public function testPost200()
    {
        $content = [
            'username' => 'test' . mt_rand(0, 1000000),
            'email' => mt_rand(0, 1000000) . '@test.com',
            'enabled' => true,
            'password' => 'abc123'
        ];

        $response = $this->runApp('POST', '/users', $content);
        $user = json_decode($response->getBody());
        $dbUser = $this->userRepository->findOneById($user->id);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($user->username, $dbUser->getUsername());
        $this->entityManager->remove($dbUser);
        $this->entityManager->flush();
    }

    public function testPost422()
    {
        $content = [
            'username' => 'user' . rand(0, 1000000),
            'enabled' => true,
            'password' => '1234'
        ];
        $response = $this->runApp('POST', '/users', $content);
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testPost400()
    {
        $dbUser = new User();
        $dbUser->setUsername("user" . rand(0, 1000000));
        $dbUser->setEmail("user" . rand(0, 1000000) . "@mail.com");
        $dbUser->setPassword("1234");
        $dbUser->setEnabled(true);
        $this->entityManager->persist($dbUser);
        $this->entityManager->flush();
        $this->entityManager->refresh($dbUser);
        $content = [
            'username' => $dbUser->getUsername(),
            'email' => 'user' . rand(0, 1000000) . '@mail.com',
            'enabled' => true,
            'password' => '1234'
        ];
        $response = $this->runApp('POST', '/users', $content);
        $body = json_decode($response->getBody());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Bad Request", $body->message);
        $this->entityManager->remove($dbUser);
        $this->entityManager->flush();
    }

    public function testPut200()
    {
        $dbUser = new User();
        $dbUser->setUsername("user" . rand(0, 1000000));
        $dbUser->setEmail("user" . rand(0, 1000000) . "@mail.com");
        $dbUser->setPassword("1234");
        $dbUser->setEnabled(true);
        $this->entityManager->persist($dbUser);
        $this->entityManager->flush();
        $this->entityManager->refresh($dbUser);
        $content = [
            'username' => 'user' . rand(0, 1000000),
            'email' => 'user' . rand(0, 1000000) . '@mail.com'
        ];
        $response = $this->runApp('PUT', '/users/' . $dbUser->getId(), $content);
        $user = json_decode($response->getBody());
        $this->entityManager->refresh($dbUser);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($user->username, $dbUser->getUsername());
    }

    public function testPut404()
    {
        $content = [
            'username' => 'user' . rand(0, 1000000),
            'email' => 'user' . rand(0, 1000000) . '@mail.com'
        ];
        $response = $this->runApp('PUT', '/users/99999999999999999999', $content);
        $body = json_decode($response->getBody());
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("Not Found", $body->message);
    }

    public function testPut400()
    {
        $dbUser = new User();
        $dbUser->setUsername("user" . rand(0, 1000000));
        $dbUser->setEmail("user" . rand(0, 1000000) . "@mail.com");
        $dbUser->setPassword("1234");
        $dbUser->setEnabled(true);
        $this->entityManager->persist($dbUser);
        $this->entityManager->flush();
        $this->entityManager->refresh($dbUser);
        $content = ['username' => $dbUser->getUsername()];
        $response = $this->runApp('PUT', '/users/' . $dbUser->getId(), $content);
        $this->assertEquals(400, $response->getStatusCode());
        $this->entityManager->remove($dbUser);
        $this->entityManager->flush();
    }

    public function testDelete204()
    {
        $dbUser = new User();
        $dbUser->setUsername("user" . rand(0, 1000000));
        $dbUser->setEmail("user" . rand(0, 1000000) . "@mail.com");
        $dbUser->setPassword("1234");
        $dbUser->setEnabled(true);
        $this->entityManager->persist($dbUser);
        $this->entityManager->flush();
        $this->entityManager->refresh($dbUser);

        $response = $this->runApp('DELETE', '/users/' . $dbUser->getId());
        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testDelete404()
    {
        $response = $this->runApp('DELETE', '/users/99999999999999999999');
        $body = json_decode($response->getBody());
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("User not found", $body->message);
    }

    public function tearDown()
    {
        unset($this->testsub);
    }

}