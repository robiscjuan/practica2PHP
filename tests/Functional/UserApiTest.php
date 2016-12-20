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
    public function testListOK()
    {
        $response = $this->runApp('GET', '/users');
        $body = json_decode($response->getBody());
        $dbUsers = $this->userRepository->findAll();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(count($body->users), count($dbUsers));
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
    public function testCreateWithEmailInUseKO()
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
            'email' => $dbUser->getEmail(),
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
    public function testRetrieveOK()
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
    public function testRetrieveNonExistentIdKO()
    {
        $response = $this->runApp('GET', '/users/9999999999999');
        $body = json_decode($response->getBody());
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("User not found", $body->message);
    }
    public function testUpdateOK()
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
    public function testUpdateWithNonExistentIdKO()
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
    public function testUpdateWithUsernameInUseKO()
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
    public function testUpdateWithEmailInUseKO()
    {
        $dbUser = new User();
        $dbUser->setUsername("user" . rand(0, 1000000));
        $dbUser->setEmail("user" . rand(0, 1000000) . "@mail.com");
        $dbUser->setPassword("1234");
        $dbUser->setEnabled(true);
        $this->entityManager->persist($dbUser);
        $this->entityManager->flush();
        $this->entityManager->refresh($dbUser);
        $content = ['email' => $dbUser->getEmail()];
        $response = $this->runApp('PUT', '/users/' . $dbUser->getId(), $content);
        $this->assertEquals(400, $response->getStatusCode());
        $this->entityManager->remove($dbUser);
        $this->entityManager->flush();
    }
    public function testDestroyOK()
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
    public function testDestroyWithNonExistentIdKO()
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