<?php // tests/Functional/UserApiTest.php
namespace MiW16\Results\Tests\Functional;
use MiW16\Results\Entity\User;

require_once '../../bootstrap.php';
require_once 'BaseTestCase.php';
/**
 * Class UserApiTest
 * @package MiW16\Results\Tests\Functional
 */
class UserApiTest extends BaseTestCase
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
        $users = $this->userRepository->findAll();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(count($body->users), count($users));
    }

    public function testGet200()
    {
        $userFromDb = new User();
        $userFromDb->setUsername("user" . rand(0, 1000000));
        $userFromDb->setEmail("user" . rand(0, 1000000) . "@mail.com");
        $userFromDb->setPassword("1234");
        $userFromDb->setEnabled(true);
        $this->entityManager->persist($userFromDb);
        $this->entityManager->flush();
        $this->entityManager->refresh($userFromDb);
        $response = $this->runApp('GET', '/users/' . $userFromDb->getId());
        $user = json_decode($response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($user->username, $userFromDb->getUsername());
        $this->entityManager->remove($userFromDb);
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
        $userFromDb = $this->userRepository->findOneById($user->id);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($user->username, $userFromDb->getUsername());
        $this->entityManager->remove($userFromDb);
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
        $userFromDb = new User();
        $userFromDb->setUsername("user" . rand(0, 1000000));
        $userFromDb->setEmail("user" . rand(0, 1000000) . "@mail.com");
        $userFromDb->setPassword("1234");
        $userFromDb->setEnabled(true);
        $this->entityManager->persist($userFromDb);
        $this->entityManager->flush();
        $this->entityManager->refresh($userFromDb);
        $content = [
            'username' => $userFromDb->getUsername(),
            'email' => 'user' . rand(0, 1000000) . '@mail.com',
            'enabled' => true,
            'password' => '1234'
        ];
        $response = $this->runApp('POST', '/users', $content);
        $body = json_decode($response->getBody());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Bad Request", $body->message);
        $this->entityManager->remove($userFromDb);
        $this->entityManager->flush();
    }

    public function testPut200()
    {
        $userFromDb = new User();
        $userFromDb->setUsername("user" . rand(0, 1000000));
        $userFromDb->setEmail("user" . rand(0, 1000000) . "@mail.com");
        $userFromDb->setPassword("1234");
        $userFromDb->setEnabled(true);
        $this->entityManager->persist($userFromDb);
        $this->entityManager->flush();
        $this->entityManager->refresh($userFromDb);
        $content = [
            'username' => 'user' . rand(0, 1000000),
            'email' => 'user' . rand(0, 1000000) . '@mail.com'
        ];
        $response = $this->runApp('PUT', '/users/' . $userFromDb->getId(), $content);
        $user = json_decode($response->getBody());
        $this->entityManager->refresh($userFromDb);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($user->username, $userFromDb->getUsername());
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
        $userFromDb = new User();
        $userFromDb->setUsername("user" . rand(0, 1000000));
        $userFromDb->setEmail("user" . rand(0, 1000000) . "@mail.com");
        $userFromDb->setPassword("1234");
        $userFromDb->setEnabled(true);
        $this->entityManager->persist($userFromDb);
        $this->entityManager->flush();
        $this->entityManager->refresh($userFromDb);
        $content = ['username' => $userFromDb->getUsername()];
        $response = $this->runApp('PUT', '/users/' . $userFromDb->getId(), $content);
        $this->assertEquals(400, $response->getStatusCode());
        $this->entityManager->remove($userFromDb);
        $this->entityManager->flush();
    }

    public function testDelete204()
    {
        $userFromDb = new User();
        $userFromDb->setUsername("user" . rand(0, 1000000));
        $userFromDb->setEmail("user" . rand(0, 1000000) . "@mail.com");
        $userFromDb->setPassword("1234");
        $userFromDb->setEnabled(true);
        $this->entityManager->persist($userFromDb);
        $this->entityManager->flush();
        $this->entityManager->refresh($userFromDb);

        $response = $this->runApp('DELETE', '/users/' . $userFromDb->getId());
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

    private function generateOKUser(){
        return [
            'username' => 'test' . mt_rand(0, 999999),
            'email' => mt_rand(0, 999999) . '@test.com',
            'enabled' => true,
            'password' => 'abc123'
        ];
    }
    private function generateIncompleteUser(){
        return [
            'enabled' => true,
            'password' => 'abc123'
        ];
    }
}