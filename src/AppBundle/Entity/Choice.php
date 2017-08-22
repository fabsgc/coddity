<?php

namespace AppBundle\Entity;

use AppBundle\Util\Now;
use Doctrine\ORM\Mapping as ORM;

/**
 * Choice
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ChoiceRepository")
 */
class Choice
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
     * @ORM\Column(type="text")
     */
    private $description;

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
     * Subscription constructor.
     */
    public function __construct() {
        $this->createdAt = Now::now();
        $this->updatedAt = Now::now();
        $this->description = '';
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
}
