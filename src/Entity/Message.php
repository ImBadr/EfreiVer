<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Type("integer")
     * @JMS\Groups({"messagerie", "message"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Groups({"messagerie", "message"})
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity=Conversation::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Groups({"message"})
     */
    private $conversation;

    /**
     * @ORM\Column(type="string", length=1000)
     * @JMS\Groups({"messagerie", "message"})
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     * @JMS\Groups({"messagerie", "message"})
     */
    private $seen;

    /**
     * @var datetime $created
     *
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"messagerie", "message"})
     */
    protected $created;

    public function __construct() {
        $this->created = new \DateTime("now");
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSeen(): ?bool
    {
        return $this->seen;
    }

    public function setSeen(bool $seen): self
    {
        $this->seen = $seen;

        return $this;
    }
}
