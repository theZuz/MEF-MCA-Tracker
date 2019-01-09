<?php declare(strict_types=1);

namespace App\Presenters;

use App\Model\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Presenter;

final class EmployeeRemovePresenter extends Presenter
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	public function __construct(
		EntityManagerInterface $entityManager
	) {
		parent::__construct();
		$this->entityManager = $entityManager;
	}

	public function actionDefault(int $id): void
	{
		/** @var Employee $employee */
		$employee = $this->entityManager->getRepository(Employee::class)->find($id);
		if ($employee && $employee->getDepartment()->getManager()->getId() === $this->getUser()->getId()) {
			$this->entityManager->remove($employee);
			$this->entityManager->flush();
		}
		$this->redirect('EmployeeList:');
	}
}
