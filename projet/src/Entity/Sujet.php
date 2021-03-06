<?php

namespace App\Entity;

use App\Repository\ReactionRepository;
use App\Repository\SujetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\DateIntervalType;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use DateInterval;


/**
 * @ORM\Entity(repositoryClass=SujetRepository::class)
 */
class Sujet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datePost;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="sujets")
     */
    private $auteur;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="sujet")
     */
    private $commentaires;

    /**
     * @ORM\OneToMany(targetEntity=Signalement::class, mappedBy="sujet")
     */
    private $signalements;

    /**
     * @ORM\OneToMany(targetEntity=Reaction::class, mappedBy="sujet",)
     */
    private $reactions;

    public function __construct()
    {
        $this->datePost = new \DateTime();
        $this->commentaires = new ArrayCollection();
        $this->signalements = new ArrayCollection();
        $this->reactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatePost(): ?\DateTimeInterface
    {
        return $this->datePost;
    }

    public function setDatePost(\DateTimeInterface $datePost): self
    {
        $this->datePost = $datePost;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getAuteur(): ?User
    {
        return $this->auteur;
    }

    public function setAuteur(?User $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setSujet($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getSujet() === $this) {
                $commentaire->setSujet(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Signalement[]
     */
    public function getSignalements(): Collection
    {
        return $this->signalements;
    }

    public function addSignalement(Signalement $signalement): self
    {
        if (!$this->signalements->contains($signalement)) {
            $this->signalements[] = $signalement;
            $signalement->setSujet($this);
        }

        return $this;
    }

    public function removeSignalement(Signalement $signalement): self
    {
        if ($this->signalements->removeElement($signalement)) {
            // set the owning side to null (unless already changed)
            if ($signalement->getSujet() === $this) {
                $signalement->setSujet(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Reaction[]
     */
    public function getReactions(): Collection
    {
        return $this->reactions;
    }

    public function addReaction(Reaction $reaction): self
    {
        if (!$this->reactions->contains($reaction)) {
            $this->reactions[] = $reaction;
            $reaction->setSujet($this);
        }

        return $this;
    }

    public function removeReaction(Reaction $reaction): self
    {
        if ($this->reactions->removeElement($reaction)) {
            // set the owning side to null (unless already changed)
            if ($reaction->getSujet() === $this) {
                $reaction->setSujet(null);
            }
        }

        return $this;
    }

    //Fonction pour savoir si l'edit est possibl
    //Si editPossible == 1 alors l'édit est possible
    //Sinon l'édit n'est pas possible
    //A savoir qu'on compte les signalements, les likes ou dislike ou les commentaires et la date de post 
    //Et si toutes les condition sont réunies alors on Peut ou pas éditer le sujet.
    public function isPossibleEdit() : bool
    {
        //On récup la date de post
        //Et on fait la différence avec la date d'aujourd'hui
        //On fait le calcul pour la transformer en minutes 
        $sujetDate = $this->getDatePost();
        $d = new \DateTime();
        $d = $d->diff($sujetDate);
        $min = $d->days * 24 * 60;
        $min += $d->h * 60;
        $min += $d->i;

        $reac = $this->getReactions();
        $com = $this->getCommentaires();
        $s = $this->getSignalements();

        // On verifie si les conditions sont réunies pour pouvoir modifier
        if((count($reac) === 0)  && (count($com) === 0) && (count($s) === 0) && $min < 30) return true;
        if($sujetDate > $d) return true;

        return false;
    }
}
