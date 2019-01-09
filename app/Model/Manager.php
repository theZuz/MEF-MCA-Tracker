<?php declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Manager
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var int|null
	 */
	private $id;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $email = '';

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $password = '';

	public function __construct(
		string $email,
		string $password
	) {
		$this->email = $email;
		$this->password = password_hash($password, PASSWORD_DEFAULT);
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function getPassword(): string
	{
		return $this->password;
	}

	public function validatePassword(string $password): bool
	{
		return password_verify($password, $this->password);
	}
}
