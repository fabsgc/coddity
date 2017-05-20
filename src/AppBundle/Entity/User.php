<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * User
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @DoctrineAssert\UniqueEntity(fields={"username"}, message="Ce nom d'utilisateur est déjà utilisé" )
 * @DoctrineAssert\UniqueEntity(fields={"email"}, message="Cet email est déjà utilisé" )
 */
class User extends BaseUser
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(length=64, nullable=true)
     * @Assert\Length(min=3, max=64)
     */
    private $firstname;

    /**
     * @var string
     * @ORM\Column(length=64, nullable=true)
     * @Assert\Length(min=3, max=64)
     */
    private $lastname;

    /**
     * @var string
     * @Gedmo\Slug(fields={"username"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @var File
     * @Assert\File(mimeTypes={ "image/jpeg", "image/png" })
     */
    private $picture;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $registrationDate;

    public function __construct() {
        parent::__construct();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {   
        return $this->id;
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
     * @return File
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param File $picture
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
     * @return bool
     */
    public function isExpired()
    {
        return !$this->isAccountNonExpired();
    }
}

