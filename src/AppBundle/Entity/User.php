<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * User
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @UniqueEntity("email")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(length=64)
     * @Assert\Length(min=3, max=64)
     */
    private $firstname;

    /**
     * @var string
     * @ORM\Column(length=64)
     * @Assert\Length(min=3, max=64)
     */
    private $lastname;

    /**
     * @var string
     * @Gedmo\Slug(fields={"firstname", "lastname"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @var string
     * @ORM\Column(length=255, nullable=true, options={"default": "default-upload-picture.png"})
     */
    private $picture;

    /**
     * @var File
     * @Assert\File(mimeTypes={ "image/jpeg", "image/png" })
     */
    private $pictureUpload;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $registrationDate;

    /**
     * @var string
     * @ORM\Column(length=32)
     */
    private $token;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {   
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
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

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return User
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     * @return User
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * @param \DateTime $registrationDate
     * @return User
     */
    public function setRegistrationDate(\DateTime $registrationDate)
    {
        $this->registrationDate = $registrationDate;
        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * */
    public function assignUsername()
    {
        $this->username = $this->email;
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
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPictureUpload()
    {
        return $this->pictureUpload;
    }

    /**
     * @param mixed $pictureUpload
     */
    public function setPictureUpload($pictureUpload)
    {
        $this->pictureUpload = $pictureUpload;
    }

    public function isExpired()
    {
        return !$this->isAccountNonExpired();
    }
}

