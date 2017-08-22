<?php

namespace AppBundle\Entity;

use AppBundle\Util\Now;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Survey
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParticipantRepository")
 * @DoctrineAssert\UniqueEntity(fields = {"email", "survey"}, message = "Vous ne pouvez pas avoir deux fois le même participant")
 */
class Participant
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
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $hasVoted = false;

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
     * @var Survey
     * @ORM\ManyToOne(targetEntity="Survey")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $survey;

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
    public function getToken(): string {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token) {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email) {
        $this->email = $email;
    }

    /**
     * @return bool
     */
    public function isHasVoted(): bool {
        return $this->hasVoted;
    }

    /**
     * @param bool $hasVoted
     */
    public function setHasVoted(bool $hasVoted) {
        $this->hasVoted = $hasVoted;
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
     * @return Survey
     */
    public function getSurvey(): Survey {
        return $this->survey;
    }

    /**
     * @param Survey $survey
     */
    public function setSurvey(Survey $survey) {
        $this->survey = $survey;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user) {
        $this->user = $user;
    }
}
