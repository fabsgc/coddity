<?php

namespace AppBundle\Entity;

use AppBundle\Util\Now;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Survey
 * @ORM\Embeddable
 */
class SurveyParticipants
{
    /**
     * @var array
     * @ORM\Column(type="array")
     * @Assert\Count(min = 1, minMessage = "Il doit y avoir au moins 1 participant")
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\Length(min = 3)
     * })
     */
    private $participants = [];

    /**
     * Subscription constructor.
     */
    public function __construct() {
    }

    /**
     * @return array
     */
    public function getChoices(): array {
        return $this->participants;
    }

    /**
     * @param array $participants
     */
    public function setEducations(array $participants) {
        $this->participants = $participants;
    }
}

