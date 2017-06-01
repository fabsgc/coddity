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
    public function getParticipants(): array {
        return $this->participants;
    }

    /**
     * @param array $participants
     */
    public function setParticipants(array $participants) {
        $this->participants = $participants;
    }
}

