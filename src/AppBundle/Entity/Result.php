<?php

namespace AppBundle\Entity;

use AppBundle\Util\Now;
use Doctrine\ORM\Mapping as ORM;

/**
 * Survey
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResultRepository")
 */
class Result
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $value;

    /**
     * @var Choice
     * @ORM\ManyToOne(targetEntity="Choice")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $choice;

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
     * @return float
     */
    public function getValue(): float {
        return $this->value;
    }

    /**
     * @param float $value
     */
    public function setValue(float $value) {
        $this->value = $value;
    }

    /**
     * @return Choice
     */
    public function getChoice(): Choice {
        return $this->choice;
    }

    /**
     * @param Choice $choice
     */
    public function setChoice(Choice $choice) {
        $this->choice = $choice;
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
