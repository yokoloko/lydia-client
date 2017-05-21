<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @var integer $id
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string $email
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     *  @var string $firstname
     * @ORM\Column(type="string")
     */
    private $firstname;

    /**
     * @var string $lastname
     * @ORM\Column(type="string")
     */
    private $lastname;

    /**
     * @var string $publicToken
     * @ORM\Column(type="string")
     */
    private $publicToken;

    private $phone;

    public function getId()
    {
        return $this->id;
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
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhone()
    {
        if ($this->phone === null){
            $this->phone = '06' . sprintf('%08d', mt_rand(0,99999999));
        }

        return $this->phone;
    }

    public function getPassword()
    {
        return '1234';
    }

    /**
     * @return string
     */
    public function getPublicToken()
    {
        return $this->publicToken;
    }

    /**
     * @param string $publicToken
     * @return User
     */
    public function setPublicToken($publicToken)
    {
        $this->publicToken = $publicToken;

        return $this;
    }
}