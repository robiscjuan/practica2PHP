<?php   // src/Entity/User.php

namespace MiW16\Results\Entity;

use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;

/**
 * User
 *
 * @ORM\Table(
 *     name="users",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(
 *              name="UNIQ_TOKEN", columns={"token"}
 *          )
 *      }
 *     )
 * @ORM\Entity
 */
class User implements \JsonSerializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=40, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=60, nullable=false)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=60, nullable=false)
     */
    private $password;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=40, nullable=false)
     */
    private $token;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = 0;
        $this->username = '';
        $this->email = '';
        $this->enabled = false;
        $this->token = sha1(uniqid('', true));
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        if ($this->isEnabled())
            $enable = 'true';
        else
            $enable = 'false';

        return sprintf(
            '- %2d: %20s %30s %7s ',
            $this->id,
            $this->username,
            $this->email,
            $enable
        );
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'isEnable' => $this->enabled
        ];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @param \DateTime $lastLogin
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
}


/**
 * @SWG\Definition(
 *     definition="User",
 *     required = { "id", "username", "email" },
 *     @SWG\Property(
 *          property    = "id",
 *          description = "User Id",
 *          type        = "integer",
 *          format      = "int32"
 *      ),
 *      @SWG\Property(
 *          property    = "username",
 *          description = "User name",
 *          type        = "string"
 *      ),
 *      @SWG\Property(
 *          property    = "email",
 *          description = "User email",
 *          type        = "string"
 *      ),
 *      @SWG\Property(
 *          property    = "token",
 *          description = "API access token",
 *          type        = "string"
 *      ),
 *      @SWG\Property(
 *          property    = "enabled",
 *          description = "Denotes if user is enabled",
 *          type        = "boolean"
 *      ),
 *      example = {
 *          "id"       = 1508,
 *          "username" = "User name",
 *          "email"    = "User email",
 *          "enabled"  = true,
 *          "token"    = "$2$6a7f5b9e15f9c4a51495"
 *     }
 * )
 * @SWG\Parameter(
 *      name        = "userId",
 *      in          = "path",
 *      description = "ID of user to fetch",
 *      required    = true,
 *      type        = "integer",
 *      format      = "int32"
 * )
 */

/**
 * @SWG\Definition(
 *      definition = "UserData",
 *      @SWG\Property(
 *          property    = "username",
 *          description = "User name",
 *          type        = "string"
 *      ),
 *      @SWG\Property(
 *          property    = "email",
 *          description = "User email",
 *          type        = "string"
 *      ),
 *      @SWG\Property(
 *          property    = "password",
 *          description = "User password",
 *          type        = "string",
 *          format      = "password"
 *      ),
 *      @SWG\Property(
 *          property    = "enabled",
 *          description = "Denotes if user is enabled",
 *          type        = "boolean"
 *      ),
 *      @SWG\Property(
 *          property    = "token",
 *          description = "User token",
 *          type        = "string"
 *      ),
 *      example = {
 *          "username"  = "User_name",
 *          "email"     = "User_email@example.com",
 *          "password"  = "User_password",
 *          "enabled"   = true,
 *          "token"     = "$2$6a7f5b9e15f9c4a51495"
 *      }
 * )
 */

/**
 * User array definition
 *
 * @SWG\Definition(
 *     definition = "UsersArray",
 *      @SWG\Property(
 *          property    = "users",
 *          description = "Users array",
 *          type        = "array",
 *          items       = {
 *              "$ref": "#/definitions/User"
 *          }
 *      )
 * )
 */