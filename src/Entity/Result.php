<?php   // src/Entity/Result.php

namespace MiW16\Results\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Result
 * @package MiW16\Results\Entity
 *
 * @ORM\Entity
 * @ORM\Table(
 *      name="results",
 *      indexes={
 *          @ORM\Index(name="FK_USER_ID_idx", columns={"user_id"})
 *      }
 *     )
 */
class Result implements \JsonSerializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="result", type="integer", nullable=false)
     */
    protected $result;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime", nullable=false)
     */
    protected $time;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * Result constructor.
     *
     * @param int $result
     * @param User $user
     * @param \DateTime $time
     */
    public function __construct($result, $user, $time)
    {
        $this->id = 0;
        $this->result = $result;
        $this->user = $user;
        $this->time = $time;
    }

    /**
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    public function __toString()
    {
        return sprintf(
            '- %2d: %10s %20s %30s',
            $this->getId(),
            $this->getResult(),
            $this->getTime()->format('Y-m-d H:i:s'),
            $this->getUser()->getUsername() . ' - ' . 'id:' . $this->getUser()->getId() . ''
        );
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'result' => $this->result,
            'time' => $this->time,
            'user' => $this->user
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
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param int $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }


}


/**
 * @SWG\Definition(
 *     definition="Result",
 *     required = { "id", "result"},
 *     @SWG\Property(
 *          property    = "id",
 *          description = "Result Id",
 *          type        = "integer",
 *          format      = "int32"
 *      ),
 *      @SWG\Property(
 *          property    = "result",
 *          description = "Result result",
 *          type        = "int32"
 *      ),
 *     @SWG\Property(
 *          property    = "user_id",
 *          description = "Result user associated",
 *          type        = "int32"
 *      ),
 *      @SWG\Property(
 *          property    = "time",
 *          description = "Result time",
 *          type        = "datetime"
 *      ),
 *      example = {
 *          "id"       = 1508,
 *          "result" = 100,
 *          "user_id"  = 1,
 *          "time"    = "2016-12-22 00:00:00"
 *     }
 * )
 * @SWG\Parameter(
 *      name        = "resultId",
 *      in          = "path",
 *      description = "ID of result to fetch",
 *      required    = true,
 *      type        = "integer",
 *      format      = "int32"
 * )
 */

/**
 * @SWG\Definition(
 *     definition = "ResultData",
 *      @SWG\Property(
 *          property    = "result",
 *          description = "Result result",
 *          type        = "integer"
 *      ),
 *     @SWG\Property(
 *          property    = "user_id",
 *          description = "Result user associated",
 *          type        = "integer"
 *      ),
 *      @SWG\Property(
 *          property    = "time",
 *          description = "Result time",
 *          type = "string",
 *          format = "date-time"
 *      ),
 *      example = {
 *          "result" = 100,
 *          "user_id"  = 1,
 *          "time"    = "2016-12-22 00:00:00"
 *     }
 * )
 */

/**
 * Result array definition
 *
 * @SWG\Definition(
 *     definition = "ResultsArray",
 *      @SWG\Property(
 *          property    = "results",
 *          description = "Results array",
 *          type        = "array",
 *          items       = {
 *              "$ref": "#/definitions/Result"
 *          }
 *      )
 * )
 */