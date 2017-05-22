<?php

namespace AppBundle\Entity;

use AppBundle\Util\Now;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Survey
 * @ORM\Embeddable
 */
class SurveyChoices
{
    /**
     * @var array
     * @ORM\Column(type="array")
     * @Assert\Count(min = 2, minMessage = "Il doit y avoir au moins deux choix")
     * @Assert\All({
     *     @Assert\NotBlank
     * })
     */
    private $choices;

    /**
     * Subscription constructor.
     */
    public function __construct() {
        $this->choices = [];
    }

    /**
     * @return array
     */
    public function getChoices(): array {
        return $this->choices;
    }

    /**
     * @param array $choices
     */
    public function setChoices(array $choices) {
        $this->choices = $choices;
    }
}

