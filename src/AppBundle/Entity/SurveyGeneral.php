<?php

namespace AppBundle\Entity;

use AppBundle\Util\Now;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Survey
 * @ORM\Embeddable
 */
class SurveyGeneral
{
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
     * Subscription constructor.
     */
    public function __construct() {
    }

    /**
     * @return string
     */
    public function getName() {
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
    public function getDescription() {
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
    public function getType() {
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
    public function isMultiple() {
        return $this->multiple;
    }

    /**
     * @param bool $multiple
     */
    public function setMultiple(bool $multiple) {
        $this->multiple = $multiple;
    }
}

