<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"messagerie", "message"})
     * @JMS\Type("integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @JMS\Groups({"messagerie", "message"})
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=Offre::class, mappedBy="user", orphanRemoval=true)
     */
    private $offres;

    /**
     * @ORM\OneToMany(targetEntity=Annonce::class, mappedBy="user", orphanRemoval=true)
     */
    private $annonces;
    
    /*
     * @ORM\OneToMany(targetEntity=Conversation::class, mappedBy="sender")
     */
    private $conversationsAsSender;

    /**
     * @ORM\OneToMany(targetEntity=Conversation::class, mappedBy="receiver")
     */
    private $conversationsAsReceiver;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function __construct()
    {
        $this->roles = array('ROLE_USER');
        $this->offres = new ArrayCollection();
        $this->annonces = new ArrayCollection();
        $this->conversationsAsSender = new ArrayCollection();
        $this->conversationsAsReceiver = new ArrayCollection();
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Offre[]
     */
    public function getOffres(): Collection
    {
        return $this->offres;
    }

    public function addOffre(Offre $offre): self
    {
        if (!$this->offres->contains($offre)) {
            $this->offres[] = $offre;
            $offre->setUser($this);
        }

        return $this;
    }
    
    /**
     * @return Collection|Offre[]
     */
    public function getConversationsAsSender(): Collection
    {
        return $this->conversationsAsSender;
    }

    public function addConversationsAsSender(Conversation $conversationsAsSender): self
    {
        if (!$this->conversationsAsSender->contains($conversationsAsSender)) {
            $this->conversationsAsSender[] = $conversationsAsSender;
            $conversationsAsSender->setSender($this);
        }

        return $this;
    }

   public function removeOffre(Offre $offre): self
    {
        if ($this->offres->removeElement($offre)) {
            // set the owning side to null (unless already changed)
            if ($offre->getUser() === $this) {
                $offre->setUser(null);
            }
        }

        return $this;
    }
    
    public function removeConversationsAsSender(Conversation $conversationsAsSender): self
    {
        if ($this->conversationsAsSender->removeElement($conversationsAsSender)) {
            // set the owning side to null (unless already changed)
            if ($conversationsAsSender->getSender() === $this) {
                $conversationsAsSender->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Annonce[]
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonce $annonce): self
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces[] = $annonce;
            $annonce->setUser($this);
        }

        return $this;
    }
    
    /*
     * @return Collection|Conversation[]
     */
    public function getConversationsAsReceiver(): Collection
    {
        return $this->conversationsAsReceiver;
    }

    public function addConversationsAsReceiver(Conversation $conversationsAsReceiver): self
    {
        if (!$this->conversationsAsReceiver->contains($conversationsAsReceiver)) {
            $this->conversationsAsReceiver[] = $conversationsAsReceiver;
            $conversationsAsReceiver->setSender($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): self
    {
        if ($this->annonces->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getUser() === $this) {
                $annonce->setUser(null);
            }
        }

        return $this;
    }
    
    public function removeConversationsAsReceiver(Conversation $conversationsAsReceiver): self
    {
        if ($this->conversationsAsReceiver->removeElement($conversationsAsReceiver)) {
            // set the owning side to null (unless already changed)
            if ($conversationsAsReceiver->getSender() === $this) {
                $conversationsAsReceiver->setSender(null);
            }
        }

        return $this;
    }
}
