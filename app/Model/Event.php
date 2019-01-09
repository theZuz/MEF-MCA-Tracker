<?php declare(strict_types=1);

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
final class Event
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
	private $name = '';

	/**
	 * @ORM\Column(type="date")
	 * @var \DateTimeInterface
	 */
	private $date;

	/**
	 * @ORM\Column(type="float")
	 * @var float
	 */
	private $price = 0.0;

	/**
	 * @ORM\ManyToMany(targetEntity=Department::class)
	 * @var Department[]|Collection
	 */
	private $departments;

	/**
	 * @ORM\ManyToMany(targetEntity=Employee::class)
	 * @var Employee[]|Collection
	 */
	private $employees;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $description = '';

	public function __construct(
		Manager $manager,
		string $name,
		\DateTimeInterface $date,
		float $price
	) {
		$this->manager = $manager;
		$this->name = $name;
		$this->date = $date;
		$this->price = $price;
		$this->departments = new ArrayCollection;
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

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getDate(): \DateTimeInterface
	{
		return $this->date;
	}

	public function setDate(\DateTimeInterface $date): void
	{
		$this->date = $date;
	}

	public function getPrice(): float
	{
		return $this->price;
	}

	public function setPrice(float $price): void
	{
		$this->price = $price;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function setDescription(string $description): void
	{
		$this->description = $description;
	}

	public function addDepartment(Department $department): void
	{
		if (!$this->departments->contains($department)) {
			$this->departments->add($department);
		}
	}

	public function removeDepartment(Department $department): void
	{
		$this->departments->removeElement($department);
	}

	public function setDepartments(Department ...$departments): void
	{
		foreach ($departments as $department) {
			$this->addDepartment($department);
		}
		foreach ($this->departments as $department) {
			if (!in_array($department, $departments, true)) {
				$this->removeDepartment($department);
			}
		}
	}

	/**
	 * @return Department[]
	 */
	public function getDepartments(): array
	{
		return $this->departments->toArray();
	}

	public function addEmployee(Employee $employee): void
	{
		if (!$this->employees->contains($employee)) {
			$this->employees->add($employee);
		}
	}

	public function removeEmployee(Employee $employee): void
	{
		$this->employees->removeElement($employee);
	}

	public function setEmployees(Employee ...$employees): void
	{
		foreach ($employees as $employee) {
			$this->addEmployee($employee);
		}
		foreach ($this->employees as $employee) {
			if (!in_array($employee, $employees, true)) {
				$this->removeEmployee($employee);
			}
		}
	}

	public function hasEmployee(Employee $employee): bool
	{
		return $this->employees->contains($employee);
	}

	/**
	 * @return Employee[]
	 */
	public function getEmployees(): array
	{
		return $this->employees->toArray();
	}
}
