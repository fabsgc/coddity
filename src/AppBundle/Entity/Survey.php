<?php

namespace AppBundle\Entity;

use AppBundle\Util\Now;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Survey
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SurveyRepository")
 */
class Survey
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     * @Assert\Length(min=3, max=64)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\Length(min=3)
     */
    private $description;

    /**
     * @var string
     * Values :
     *   CHOICE
     *   DATE
     * @ORM\Column(type="string", length=55)
     * @Assert\Choice({"CHOICE", "DATE"})
     */
    private $type = 'CHOICE';

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $multiple = true;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $opened = true;

    /**
     * @var Choice
     * @ORM\ManyToOne(targetEntity="Choice")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $winner;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    /**
     * Subscription constructor.
     */
    public function __construct() {
        $this->createdAt = Now::now();
        $this->updatedAt = Now::now();
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description) {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type) {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isMultiple(): bool {
        return $this->multiple;
    }

    /**
     * @param bool $multiple
     */
    public function setMultiple(bool $multiple) {
        $this->multiple = $multiple;
    }

    /**
     * @return bool
     */
    public function isOpened(): bool {
        return $this->opened;
    }

    /**
     * @param bool $opened
     */
    public function setOpened(bool $opened) {
        $this->opened = $opened;
    }

    /**
     * @return Choice
     */
    public function getWinner() {
        return $this->winner;
    }

    /**
     * @param Choice $winner
     */
    public function setWinner(Choice $winner) {
        $this->winner = $winner;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt) {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt) {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user) {
        $this->user = $user;
    }
}

