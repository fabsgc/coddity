<?php

namespace AppBundle\Entity;

use AppBundle\Util\Now;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use AppBundle\Entity\Location;
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
 * @ORM\DiscriminatorMap({"intern" = "User", "professional" = "Professional", "customer" = "Customer"})
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
     * @ORM\Column(name="title", type="string")
     * @Assert\Choice({"M", "MME", "Maitre"})
     */
    private $title;


    /**
     * @var \DateTime
     * @ORM\Column(name="birthDate", type="date", nullable=true)
     * @Assert\Date
     */
    private $birthDate;

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
     * @var string
     * @ORM\Column(length=12)
     * @Assert\Regex("/^\d{10}$/", message="profile.phone_number.incorrect")
     */
    private $phoneNumber;

    /**
     * @var Location
     * @ORM\Embedded(class="Location")
     */
    private $location;

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
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="Professional", mappedBy="accountManager")
	 */
	protected $ownedProfessionals;

    public function __construct()
    {
        parent::__construct();
        $this->registrationDate = Now::now();
        $this->token = md5(uniqid(rand(), true));
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

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getBirthDate()
    {
        return $this->birthDate;
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
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     * @return User
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return \AppBundle\Entity\Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param \AppBundle\Entity\Location $location
     * @return User
     */
    public function setLocation($location)
    {
        $this->location = $location;
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
     * @return string
     */
    public function getType()
    {
        return 'intern';
    }

	/**
	 * @return ArrayCollection
	 */
	public function getOwnedProfessionals() {
		return $this->ownedProfessionals;
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

