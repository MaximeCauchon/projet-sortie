<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet email.')]
#[UniqueEntity(fields: ['pseudo'], message: 'Ce pseudo est déjà pris.')]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 180, unique: true)]
	#[Assert\NotBlank(message: "Veuillez renseigner un email pour ce participant !")]
	#[Assert\Email(
		message: 'L\'email {{ value }} n\'est pas valide.',
	)]
	#[Assert\Type('string')]
	private ?string $email = null;

	#[ORM\Column]
	private array $roles = [];

	/**
	 * @var string The hashed password
	 */
	#[ORM\Column]
	#[Assert\Type('string')]
	private ?string $password = null;

	#[ORM\Column(length: 100)]
	#[Assert\NotBlank(message: "Veuillez renseigner un nom pour ce participant !")]
	#[Assert\Type('string')]
	private ?string $nom = null;

	#[ORM\Column(length: 100)]
	#[Assert\NotBlank(message: "Veuillez renseigner un prénom pour ce participant !")]
	#[Assert\Type('string')]
	private ?string $prenom = null;

	#[ORM\Column(length: 50, nullable: true)]
	#[Assert\Type('string')]
	private ?string $telephone = null;

	#[ORM\Column]
	#[Assert\NotBlank(message: "Veuillez renseigner si le participant est actif ou non !")]
	private ?bool $isActif = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $image = null;

	#[ORM\Column(length: 255)]
	#[Assert\NotBlank(message: "Veuillez renseigner un pseudo pour ce participant !")]
	private ?string $pseudo = null;

	#[ORM\ManyToMany(targetEntity: Sortie::class, inversedBy: 'participants')]
	private Collection $estInscrit;

	#[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Sortie::class)]
	private Collection $organise;

	#[ORM\ManyToOne(inversedBy: 'participants')]
	#[ORM\JoinColumn(nullable: false)]
	#[Assert\NotBlank(message: "Veuillez renseigner un campus pour ce participant !")]
	private ?Campus $campus = null;

	public function __construct()
	{
		$this->estInscrit = new ArrayCollection();
		$this->organise = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(string $email): static
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * A visual identifier that represents this user.
	 *
	 * @see UserInterface
	 */
	public function getUserIdentifier(): string
	{
		return (string)$this->email;
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

	public function setRoles(array $roles): static
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

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials(): void
	{
		// If you store any temporary, sensitive data on the user, clear it here
		// $this->plainPassword = null;
	}

	public function getNom(): ?string
	{
		return $this->nom;
	}

	public function setNom(string $nom): static
	{
		$this->nom = $nom;

		return $this;
	}

	public function getPrenom(): ?string
	{
		return $this->prenom;
	}

	public function setPrenom(string $prenom): static
	{
		$this->prenom = $prenom;

		return $this;
	}

	public function getTelephone(): ?string
	{
		return $this->telephone;
	}

	public function setTelephone(?string $telephone): static
	{
		$this->telephone = $telephone;

		return $this;
	}

	public function isIsActif(): ?bool
	{
		return $this->isActif;
	}

	public function setIsActif(bool $isActif): static
	{
		$this->isActif = $isActif;

		return $this;
	}

	public function getImage(): ?string
	{
		return $this->image;
	}

	public function setImage(?string $image): static
	{
		$this->image = $image;

		return $this;
	}

	public function getPseudo(): ?string
	{
		return $this->pseudo;
	}

	public function setPseudo(string $pseudo): static
	{
		$this->pseudo = $pseudo;

		return $this;
	}

	/**
	 * @return Collection<int, sortie>
	 */
	public function getEstInscrit(): Collection
	{
		return $this->estInscrit;
	}

	public function addEstInscrit(sortie $estInscrit): static
	{
		if (!$this->estInscrit->contains($estInscrit)) {
			$this->estInscrit->add($estInscrit);
		}

		return $this;
	}

	public function removeEstInscrit(sortie $estInscrit): static
	{
		$this->estInscrit->removeElement($estInscrit);

		return $this;
	}

	/**
	 * @return Collection<int, sortie>
	 */
	public function getOrganise(): Collection
	{
		return $this->organise;
	}

	public function addOrganise(sortie $organise): static
	{
		if (!$this->organise->contains($organise)) {
			$this->organise->add($organise);
			$organise->setOrganisateur($this);
		}

		return $this;
	}

	public function removeOrganise(sortie $organise): static
	{
		if ($this->organise->removeElement($organise)) {
			// set the owning side to null (unless already changed)
			if ($organise->getOrganisateur() === $this) {
				$organise->setOrganisateur(null);
			}
		}

		return $this;
	}

	public function getCampus(): ?campus
	{
		return $this->campus;
	}

	public function setCampus(?campus $campus): static
	{
		$this->campus = $campus;

		return $this;
	}

	public function participantInscritSortie(Sortie $sortie): bool
	{
		if ($this->getEstInscrit()->contains($sortie)) {
			return true;
		} else {
			return false;
		}
	}

	public function participantOrganisateurSortie(Sortie $sortie): bool
	{
		if ($this->getOrganise()->contains($sortie)) {
			return true;
		} else {
			return false;
		}
	}

	public function isAdmin(): bool
	{
		return in_array('ROLE_ADMIN', $this->getRoles());
	}

	public function desinscriptionDeTousLesEvenementsFuturs(): string
	{
		$count = 0;
		$message = "Le participant $this->pseudo a été désinscrit des sorties suivantes : <ul>";
		foreach ($this->getEstInscrit() as $s) {
			if ($s->getDateHeureDebut() > new \DateTime()) {
				$count ++;
				$message .= "<li>" . $s->getNom() . "</li>";
				$this->removeEstInscrit($s);
			}
		}
		$message .= "</ul>";
		if ($count==0){
			return "";
		}
		return $message;
	}

	public function annulationDesEvenementsOrganisateur(EtatRepository $etatRepo): string
	{
		$count = 0;

		$message = "Les sorties suivantes ont été annulés : <ul>";
		foreach ($this->getOrganise() as $s) {
			if ($s->getDateHeureDebut() > new \DateTime() && $s->isNotAnnule()) {
				$count ++;
				$message .= "<li>" . $s->getNom() . "</li>";
				$s->annulerSortie("Annulé car organisateur/organisatrice éjecté de la plateforme.", $etatRepo);
			}
		}
		$message .= "</ul>";
		if ($count==0){
			return "";
		}
		return $message;
	}
}
