<?php declare(strict_types=1);

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Department
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var int|null
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity=Manager::class)
	 * @var Manager
	 */
	private $manager;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $code = '';

	/**
	 * @ORM\OneToMany(targetEntity=Employee::class, mappedBy="department")
	 * @var Employee[]|Collection
	 */
	private $employees;

	public function __construct(
		Manager $manager,
		string $code
	) {
		$this->manager = $manager;
		$this->code = $code;
		$this->employees = new ArrayCollection;
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getManager(): Manager
	{
		return $this->manager;
	}

	public function getCode(): string
	{
		return $this->code;
	}

	/**
	 * @return Employee[]
	 */
	public function getEmployees(): array
	{
		return $this->employees->toArray();
	}
}
