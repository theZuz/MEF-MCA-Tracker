<?php declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Employee
{
	private const YEAR_BUDGET = 50.0;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var int|null
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity=Department::class)
	 * @var Department
	 */
	private $department;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $cnum;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $country;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTimeInterface|null
	 */
	private $startDate;

	/**
	 * @ ORM\Column(type="float")
	 * @var float
	 */
	private $yearBudget = 50.0;

	public function __construct(
		Department $department,
		string $cnum,
		string $name,
		string $country
	) {
		$this->department = $department;
		$this->cnum = $cnum;
		$this->name = $name;
		$this->country = $country;
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getDepartment(): Department
	{
		return $this->department;
	}

	public function getCnum(): string
	{
		return $this->cnum;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getCountry(): string
	{
		return $this->country;
	}

	public function getStartDate(): ?\DateTimeInterface
	{
		return $this->startDate;
	}

	public function setStartDate(?\DateTimeInterface $startDate): void
	{
		$this->startDate = $startDate;
	}

	public function getBudget(\DateTimeInterface $time = null): float
	{
		if (!$time) {
			$time = new \DateTime;
		}
		$startDate = $this->startDate ? (new \DateTime)->setDate((int)$this->startDate->format('Y'), (int)$this->startDate->format('m'), 1) : null;
		if (!$startDate || $startDate->format('Y') < $time->format('Y')) {
			return self::YEAR_BUDGET;
		} elseif ($startDate->format('Y') > $time->format('Y')) {
			return 0.0;
		}
		$startOfYear = (new \DateTime)->setDate((int)$time->format('Y'), 1, 1);;
		$endOfYear = (new \DateTime)->setDate((int)$time->format('Y'), 12, 31);

		return round(self::YEAR_BUDGET / $endOfYear->diff($startOfYear)->format('%a') * $endOfYear->diff($startDate)->format('%a'));
	}
}
